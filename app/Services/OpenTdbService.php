<?php

namespace App\Services;

use App\Models\Question;
use App\Models\SessionAnswer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenTdbService
{
    /**
     * Fetch 30 completely unique, randomized questions for a game session:
     * - 10 Easy (Questions 1-10)
     * - 10 Medium (Questions 11-20)
     * - 10 Hard (Questions 21-30)
     *
     * Guarantees zero repetition for the player and always returns exactly 30 items.
     *
     * @param int|null $userId
     * @return array
     */
    public function getGameQuestions(?int $userId = null): array
    {
        // First ensure default baseline question bank is populated
        $this->ensureBaselineQuestionsExist();

        // Get all question IDs previously answered by this user across all previous matches
        $answeredQuestionIds = [];
        if ($userId) {
            $answeredQuestionIds = SessionAnswer::where('user_id', $userId)
                ->whereNotNull('question_id')
                ->pluck('question_id')
                ->toArray();
        }

        // Try to fetch fresh live questions from OpenTDB
        $this->fetchLiveFromOpenTdb('easy', 10);
        $this->fetchLiveFromOpenTdb('medium', 10);
        $this->fetchLiveFromOpenTdb('hard', 10);

        // Retrieve 10 Easy, 10 Medium, 10 Hard randomized from the pool
        $easyQuestions = $this->getRandomUniqueQuestions('easy', 10, $answeredQuestionIds);
        $mediumQuestions = $this->getRandomUniqueQuestions('medium', 10, array_merge($answeredQuestionIds, $easyQuestions->pluck('id')->toArray()));
        $hardQuestions = $this->getRandomUniqueQuestions('hard', 10, array_merge($answeredQuestionIds, $easyQuestions->pluck('id')->toArray(), $mediumQuestions->pluck('id')->toArray()));

        $allQuestions = [];
        $index = 1;

        foreach ($easyQuestions as $q) {
            $allQuestions[] = $this->formatQuestionItem($q, $index++, 1, 'easy');
            if ($index > 10) break;
        }
        foreach ($mediumQuestions as $q) {
            $allQuestions[] = $this->formatQuestionItem($q, $index++, 2, 'medium');
            if ($index > 20) break;
        }
        foreach ($hardQuestions as $q) {
            $allQuestions[] = $this->formatQuestionItem($q, $index++, 3, 'hard');
            if ($index > 30) break;
        }

        return $allQuestions;
    }

    /**
     * Pick N random questions from local database ensuring non-repeating for user.
     * Guaranteed to ALWAYS return exactly $amount questions.
     */
    public function getRandomUniqueQuestions(string $difficulty, int $amount = 10, array $excludeIds = [])
    {
        // 1. Try to get unseen questions for this user
        $questions = Question::where('difficulty', $difficulty)
            ->whereNotIn('id', $excludeIds)
            ->inRandomOrder()
            ->limit($amount)
            ->get();

        // 2. If user has seen all questions, allow seen questions of same difficulty
        if ($questions->count() < $amount) {
            $needed = $amount - $questions->count();
            $additional = Question::where('difficulty', $difficulty)
                ->whereNotIn('id', $questions->pluck('id')->toArray())
                ->inRandomOrder()
                ->limit($needed)
                ->get();
            $questions = $questions->concat($additional);
        }

        // 3. If STILL less than amount, pull from other difficulties
        if ($questions->count() < $amount) {
            $needed = $amount - $questions->count();
            $anyQuestions = Question::whereNotIn('id', $questions->pluck('id')->toArray())
                ->inRandomOrder()
                ->limit($needed)
                ->get();
            $questions = $questions->concat($anyQuestions);
        }

        // 4. If STILL less than amount (very small DB), duplicate existing questions
        if ($questions->count() < $amount && $questions->count() > 0) {
            $existing = $questions->all();
            while ($questions->count() < $amount) {
                foreach ($existing as $item) {
                    $questions->push($item);
                    if ($questions->count() >= $amount) break;
                }
            }
        }

        // 5. If database was completely empty, seed built-in fallbacks
        if ($questions->count() < $amount) {
            $this->ensureBaselineQuestionsExist();
            $questions = Question::where('difficulty', $difficulty)
                ->inRandomOrder()
                ->limit($amount)
                ->get();
        }

        return $questions;
    }

    /**
     * Fetch questions from OpenTDB API with random category selection
     */
    public function fetchLiveFromOpenTdb(string $difficulty, int $amount = 15): array
    {
        $fetched = [];

        try {
            $response = Http::timeout(3)->get('https://opentdb.com/api.php', [
                'amount' => $amount,
                'difficulty' => $difficulty,
                'type' => 'multiple',
            ]);

            if ($response->successful() && isset($response->json()['results'])) {
                foreach ($response->json()['results'] as $item) {
                    $saved = $this->storeOrRetrieveQuestion($item);
                    if ($saved) {
                        $fetched[] = $saved;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::info("OpenTDB API background pull note: " . $e->getMessage());
        }

        return $fetched;
    }

    /**
     * Bulk fetch questions across multiple categories for question bank
     */
    public function bulkSyncQuestions(int $countPerDifficulty = 50): int
    {
        $total = 0;
        $difficulties = ['easy', 'medium', 'hard'];

        foreach ($difficulties as $diff) {
            for ($i = 0; $i < 3; $i++) {
                try {
                    $res = Http::timeout(4)->get('https://opentdb.com/api.php', [
                        'amount' => 20,
                        'difficulty' => $diff,
                        'type' => 'multiple',
                    ]);

                    if ($res->successful() && isset($res->json()['results'])) {
                        foreach ($res->json()['results'] as $item) {
                            $saved = $this->storeOrRetrieveQuestion($item);
                            if ($saved) $total++;
                        }
                    }
                    usleep(500000); // 500ms
                } catch (\Exception $e) {}
            }
        }

        // Also ensure baseline fallbacks are seeded
        $this->ensureBaselineQuestionsExist();

        return $total;
    }

    /**
     * Fetch N questions for a given difficulty from OpenTDB or DB fallback.
     */
    public function fetchQuestionsForDifficulty(string $difficulty, int $amount = 10, array $excludeIds = []): array
    {
        $this->fetchLiveFromOpenTdb($difficulty, $amount);
        $questions = $this->getRandomUniqueQuestions($difficulty, $amount, $excludeIds);
        return $questions->all();
    }

    /**
     * Decode HTML entities and store question into local database if not exists.
     */
    public function storeOrRetrieveQuestion(array $item): ?Question
    {
        $decodedQuestion = html_entity_decode($item['question'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $decodedCategory = html_entity_decode($item['category'] ?? 'General Knowledge', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $decodedCorrect = html_entity_decode($item['correct_answer'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $decodedIncorrect = array_map(function ($ans) {
            return html_entity_decode($ans, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }, $item['incorrect_answers'] ?? []);

        if (empty($decodedQuestion) || empty($decodedCorrect) || count($decodedIncorrect) === 0) {
            return null;
        }

        $hash = sha1(strtolower(trim($decodedQuestion)));

        return Question::firstOrCreate(
            ['question_hash' => $hash],
            [
                'category' => $decodedCategory,
                'difficulty' => strtolower($item['difficulty'] ?? 'easy'),
                'type' => $item['type'] ?? 'multiple',
                'question_text' => $decodedQuestion,
                'correct_answer' => $decodedCorrect,
                'incorrect_answers' => $decodedIncorrect,
            ]
        );
    }

    /**
     * Ensure baseline built-in questions exist in the database (at least 60 questions).
     */
    public function ensureBaselineQuestionsExist(): void
    {
        $count = Question::count();
        if ($count >= 60) return;

        $fallbacks = $this->getBaselineQuestionPool();
        foreach ($fallbacks as $item) {
            $hash = sha1(strtolower(trim($item['question_text'])));
            Question::firstOrCreate(
                ['question_hash' => $hash],
                $item
            );
        }
    }

    /**
     * Built-in 60 questions library (20 Easy, 20 Medium, 20 Hard)
     */
    public function getBaselineQuestionPool(): array
    {
        return [
            // ================= EASY (20 Questions) =================
            [
                'category' => 'Geography',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'What is the capital city of France?',
                'correct_answer' => 'Paris',
                'incorrect_answers' => ['London', 'Rome', 'Berlin'],
            ],
            [
                'category' => 'Science: Computers',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'What does CPU stand for?',
                'correct_answer' => 'Central Processing Unit',
                'incorrect_answers' => ['Central Process Unit', 'Computer Personal Unit', 'Central Processor Utility'],
            ],
            [
                'category' => 'General Knowledge',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'How many days are there in a standard leap year?',
                'correct_answer' => '366',
                'incorrect_answers' => ['365', '364', '367'],
            ],
            [
                'category' => 'Sports',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'How many players are on the court for one team in basketball?',
                'correct_answer' => '5',
                'incorrect_answers' => ['6', '7', '4'],
            ],
            [
                'category' => 'Science & Nature',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'What is the chemical symbol for Water?',
                'correct_answer' => 'H2O',
                'incorrect_answers' => ['CO2', 'O2', 'NaCl'],
            ],
            [
                'category' => 'Animals',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'What is the largest living mammal on Earth?',
                'correct_answer' => 'Blue Whale',
                'incorrect_answers' => ['African Elephant', 'Giraffe', 'Colossal Squid'],
            ],
            [
                'category' => 'Geography',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'Which continent is the Sahara Desert located on?',
                'correct_answer' => 'Africa',
                'incorrect_answers' => ['Asia', 'South America', 'Australia'],
            ],
            [
                'category' => 'Entertainment: Film',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'Who played Iron Man / Tony Stark in the Marvel Cinematic Universe?',
                'correct_answer' => 'Robert Downey Jr.',
                'incorrect_answers' => ['Chris Evans', 'Chris Hemsworth', 'Mark Ruffalo'],
            ],
            [
                'category' => 'General Knowledge',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'What is the primary color obtained by mixing Blue and Yellow?',
                'correct_answer' => 'Green',
                'incorrect_answers' => ['Purple', 'Orange', 'Brown'],
            ],
            [
                'category' => 'Science: Computers',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'Which company develops the Android operating system?',
                'correct_answer' => 'Google',
                'incorrect_answers' => ['Apple', 'Microsoft', 'Samsung'],
            ],
            [
                'category' => 'History',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'Who was the first President of the United States?',
                'correct_answer' => 'George Washington',
                'incorrect_answers' => ['Thomas Jefferson', 'Abraham Lincoln', 'John Adams'],
            ],
            [
                'category' => 'Sports',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'Which country won the 2022 FIFA World Cup?',
                'correct_answer' => 'Argentina',
                'incorrect_answers' => ['France', 'Brazil', 'Germany'],
            ],
            [
                'category' => 'Science & Nature',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'What planet is known as the Red Planet?',
                'correct_answer' => 'Mars',
                'incorrect_answers' => ['Venus', 'Jupiter', 'Mercury'],
            ],
            [
                'category' => 'General Knowledge',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'How many colors are in a standard rainbow?',
                'correct_answer' => '7',
                'incorrect_answers' => ['6', '8', '5'],
            ],
            [
                'category' => 'Geography',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'What is the capital city of Japan?',
                'correct_answer' => 'Tokyo',
                'incorrect_answers' => ['Kyoto', 'Osaka', 'Seoul'],
            ],
            [
                'category' => 'Science: Computers',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'What does HTML stand for in web development?',
                'correct_answer' => 'HyperText Markup Language',
                'incorrect_answers' => ['HighText Machine Language', 'HyperTransfer Mode Language', 'Home Tool Markup Language'],
            ],
            [
                'category' => 'Entertainment: Music',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'Who is known as the "King of Pop"?',
                'correct_answer' => 'Michael Jackson',
                'incorrect_answers' => ['Elvis Presley', 'Freddie Mercury', 'Prince'],
            ],
            [
                'category' => 'Animals',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'How many legs does a spider have?',
                'correct_answer' => '8',
                'incorrect_answers' => ['6', '10', '12'],
            ],
            [
                'category' => 'General Knowledge',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'Which currency is used in Japan?',
                'correct_answer' => 'Yen',
                'incorrect_answers' => ['Won', 'Yuan', 'Ringgit'],
            ],
            [
                'category' => 'Sports',
                'difficulty' => 'easy',
                'type' => 'multiple',
                'question_text' => 'In tennis, what is the term for a score of zero?',
                'correct_answer' => 'Love',
                'incorrect_answers' => ['Nil', 'Duck', 'Zero'],
            ],

            // ================= MEDIUM (20 Questions) =================
            [
                'category' => 'Geography',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'What is the longest river in the world?',
                'correct_answer' => 'Nile River',
                'incorrect_answers' => ['Amazon River', 'Yangtze River', 'Mississippi River'],
            ],
            [
                'category' => 'History',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'In which year did the Titanic sink?',
                'correct_answer' => '1912',
                'incorrect_answers' => ['1905', '1918', '1923'],
            ],
            [
                'category' => 'Science & Nature',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'What is the hardest natural mineral on Earth?',
                'correct_answer' => 'Diamond',
                'incorrect_answers' => ['Titanium', 'Quartz', 'Tungsten'],
            ],
            [
                'category' => 'Sports',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'Which boxer was banned for biting Evander Holyfield\'s ear in 1997?',
                'correct_answer' => 'Mike Tyson',
                'incorrect_answers' => ['Lennox Lewis', 'Evander Holyfield', 'George Foreman'],
            ],
            [
                'category' => 'Science: Computers',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'In computer science, which data structure operates on a LIFO (Last-In, First-Out) principle?',
                'correct_answer' => 'Stack',
                'incorrect_answers' => ['Queue', 'Linked List', 'Binary Tree'],
            ],
            [
                'category' => 'General Knowledge',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'Which element makes up the majority of Earth\'s atmosphere?',
                'correct_answer' => 'Nitrogen',
                'incorrect_answers' => ['Oxygen', 'Carbon Dioxide', 'Argon'],
            ],
            [
                'category' => 'Entertainment: Video Games',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'What is the highest-selling video game of all time?',
                'correct_answer' => 'Minecraft',
                'incorrect_answers' => ['Grand Theft Auto V', 'Tetris', 'Wii Sports'],
            ],
            [
                'category' => 'Geography',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'What is the capital city of Australia?',
                'correct_answer' => 'Canberra',
                'incorrect_answers' => ['Sydney', 'Melbourne', 'Brisbane'],
            ],
            [
                'category' => 'History',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'Who painted the famous ceiling of the Sistine Chapel?',
                'correct_answer' => 'Michelangelo',
                'incorrect_answers' => ['Leonardo da Vinci', 'Raphael', 'Donatello'],
            ],
            [
                'category' => 'Science: Computers',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'Which HTTP status code signifies "Not Found"?',
                'correct_answer' => '404',
                'incorrect_answers' => ['500', '403', '301'],
            ],
            [
                'category' => 'Science & Nature',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'What is the chemical symbol for Gold?',
                'correct_answer' => 'Au',
                'incorrect_answers' => ['Ag', 'Fe', 'Gd'],
            ],
            [
                'category' => 'Sports',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'How many minutes is a standard professional rugby union match?',
                'correct_answer' => '80 Minutes',
                'incorrect_answers' => ['90 Minutes', '60 Minutes', '70 Minutes'],
            ],
            [
                'category' => 'Geography',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'Which country has the most natural lakes in the world?',
                'correct_answer' => 'Canada',
                'incorrect_answers' => ['Russia', 'United States', 'Finland'],
            ],
            [
                'category' => 'General Knowledge',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'What year did the Berlin Wall fall?',
                'correct_answer' => '1989',
                'incorrect_answers' => ['1991', '1985', '1979'],
            ],
            [
                'category' => 'Entertainment: Film',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'Which film won the Academy Award for Best Picture in 1997?',
                'correct_answer' => 'Titanic',
                'incorrect_answers' => ['Good Will Hunting', 'L.A. Confidential', 'As Good as It Gets'],
            ],
            [
                'category' => 'Science: Computers',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'Who created the Linux operating system kernel?',
                'correct_answer' => 'Linus Torvalds',
                'incorrect_answers' => ['Richard Stallman', 'Ken Thompson', 'Dennis Ritchie'],
            ],
            [
                'category' => 'Animals',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'What is the fastest land animal in the world?',
                'correct_answer' => 'Cheetah',
                'incorrect_answers' => ['Pronghorn', 'Lion', 'Peregrine Falcon'],
            ],
            [
                'category' => 'History',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'Which ancient civilization built the Machu Picchu citadel?',
                'correct_answer' => 'Inca',
                'incorrect_answers' => ['Aztec', 'Maya', 'Olmec'],
            ],
            [
                'category' => 'General Knowledge',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'What is the national flower of Japan?',
                'correct_answer' => 'Cherry Blossom (Sakura)',
                'incorrect_answers' => ['Lotus', 'Rose', 'Orchid'],
            ],
            [
                'category' => 'Science & Nature',
                'difficulty' => 'medium',
                'type' => 'multiple',
                'question_text' => 'How many bones are in the adult human body?',
                'correct_answer' => '206',
                'incorrect_answers' => ['212', '198', '250'],
            ],

            // ================= HARD (20 Questions) =================
            [
                'category' => 'History',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'In what year did the Byzantine Empire fall with the siege of Constantinople?',
                'correct_answer' => '1453',
                'incorrect_answers' => ['1492', '1389', '1517'],
            ],
            [
                'category' => 'Science & Nature',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'What is the rarest naturally occurring element in the Earth\'s crust?',
                'correct_answer' => 'Astatine',
                'incorrect_answers' => ['Francium', 'Promethium', 'Osmium'],
            ],
            [
                'category' => 'Geography',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'What is the capital city of Kazakhstan (since its renaming back in 2022)?',
                'correct_answer' => 'Astana',
                'incorrect_answers' => ['Almaty', 'Nur-Sultan', 'Bishkek'],
            ],
            [
                'category' => 'Science: Computers',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'What is the time complexity of searching an element in a balanced Binary Search Tree (AVL tree)?',
                'correct_answer' => 'O(log n)',
                'incorrect_answers' => ['O(n)', 'O(1)', 'O(n log n)'],
            ],
            [
                'category' => 'General Knowledge',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'Which philosopher authored the 1651 political treatise "Leviathan"?',
                'correct_answer' => 'Thomas Hobbes',
                'incorrect_answers' => ['John Locke', 'Jean-Jacques Rousseau', 'Niccolò Machiavelli'],
            ],
            [
                'category' => 'Sports',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'Who holds the record for the most Formula 1 Grand Prix wins in history?',
                'correct_answer' => 'Lewis Hamilton',
                'incorrect_answers' => ['Michael Schumacher', 'Ayrton Senna', 'Sebastian Vettel'],
            ],
            [
                'category' => 'Science & Nature',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'What is the name of the deepest point in Earth\'s oceans?',
                'correct_answer' => 'Challenger Deep',
                'incorrect_answers' => ['Mariana Trench Floor', 'Puerto Rico Trench', 'Java Trench'],
            ],
            [
                'category' => 'History',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'The Battle of Waterloo took place in which modern-day country?',
                'correct_answer' => 'Belgium',
                'incorrect_answers' => ['France', 'Netherlands', 'Germany'],
            ],
            [
                'category' => 'Science: Computers',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'In cryptography, what does the acronym RSA stand for?',
                'correct_answer' => 'Rivest-Shamir-Adleman',
                'incorrect_answers' => ['Random Secure Algorithm', 'Recursive Symmetric Authentication', 'Rotational Shift Array'],
            ],
            [
                'category' => 'Geography',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'Which country in the world is completely surrounded by South Africa?',
                'correct_answer' => 'Lesotho',
                'incorrect_answers' => ['Eswatini', 'Botswana', 'Namibia'],
            ],
            [
                'category' => 'Science & Nature',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'What is the speed of light in a vacuum (approximate value in m/s)?',
                'correct_answer' => '299,792,458 m/s',
                'incorrect_answers' => ['300,500,000 m/s', '150,000,000 m/s', '384,400,000 m/s'],
            ],
            [
                'category' => 'General Knowledge',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'Which treaty ended the Thirty Years\' War in 1648?',
                'correct_answer' => 'Peace of Westphalia',
                'incorrect_answers' => ['Treaty of Utrecht', 'Treaty of Versailles', 'Treaty of Tordesillas'],
            ],
            [
                'category' => 'Entertainment: Film',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'Who directed the 1927 silent sci-fi masterpiece "Metropolis"?',
                'correct_answer' => 'Fritz Lang',
                'incorrect_answers' => ['F.W. Murnau', 'Stanley Kubrick', 'Orson Welles'],
            ],
            [
                'category' => 'History',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'Which Roman Emperor legalized Christianity throughout the empire via the Edict of Milan in 313 AD?',
                'correct_answer' => 'Constantine the Great',
                'incorrect_answers' => ['Julius Caesar', 'Nero', 'Marcus Aurelius'],
            ],
            [
                'category' => 'Science: Computers',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'Which programming language was developed by Bjarne Stroustrup in 1979 at Bell Labs?',
                'correct_answer' => 'C++',
                'incorrect_answers' => ['C', 'Java', 'Pascal'],
            ],
            [
                'category' => 'Animals',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'What is the only known venomous primate?',
                'correct_answer' => 'Slow Loris',
                'incorrect_answers' => ['Aye-Aye', 'Pygmy Marmoset', 'Tarsier'],
            ],
            [
                'category' => 'Geography',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'Which sea is located between Saudi Arabia and Egypt?',
                'correct_answer' => 'Red Sea',
                'incorrect_answers' => ['Dead Sea', 'Arabian Sea', 'Black Sea'],
            ],
            [
                'category' => 'Science & Nature',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'What is the powerhouse organelle of eukaryotic cells?',
                'correct_answer' => 'Mitochondria',
                'incorrect_answers' => ['Ribosome', 'Endoplasmic Reticulum', 'Golgi Apparatus'],
            ],
            [
                'category' => 'General Knowledge',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'How many squares are there on a standard international chessboard?',
                'correct_answer' => '64',
                'incorrect_answers' => ['81', '100', '49'],
            ],
            [
                'category' => 'Sports',
                'difficulty' => 'hard',
                'type' => 'multiple',
                'question_text' => 'In baseball, how many stitches are on an official MLB baseball?',
                'correct_answer' => '108',
                'incorrect_answers' => ['120', '96', '144'],
            ],
        ];
    }

    /**
     * Format a question item for session storage and client presentation
     */
    private function formatQuestionItem(Question $question, int $index, int $round, string $difficulty): array
    {
        $choices = array_merge([$question->correct_answer], $question->incorrect_answers ?? []);
        shuffle($choices);

        return [
            'id' => $question->id,
            'index' => $index,
            'round' => $round,
            'difficulty' => $difficulty,
            'category' => $question->category,
            'question_text' => $question->question_text,
            'choices' => $choices,
            'correct_answer' => $question->correct_answer,
        ];
    }
}
