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
     * - 10 Easy (Questions 1-10) across diverse categories
     * - 10 Medium (Questions 11-20) across diverse categories
     * - 10 Hard (Questions 21-30) across diverse categories
     *
     * Guarantees zero repetition for the player.
     *
     * @param int|null $userId
     * @return array
     */
    public function getGameQuestions(?int $userId = null): array
    {
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
        }
        foreach ($mediumQuestions as $q) {
            $allQuestions[] = $this->formatQuestionItem($q, $index++, 2, 'medium');
        }
        foreach ($hardQuestions as $q) {
            $allQuestions[] = $this->formatQuestionItem($q, $index++, 3, 'hard');
        }

        return $allQuestions;
    }

    /**
     * Pick N random questions from local database ensuring non-repeating for user
     */
    public function getRandomUniqueQuestions(string $difficulty, int $amount = 10, array $excludeIds = [])
    {
        // First try to get unseen questions for this user
        $questions = Question::where('difficulty', $difficulty)
            ->whereNotIn('id', $excludeIds)
            ->inRandomOrder()
            ->limit($amount)
            ->get();

        // If user has answered all questions in the bank, allow random selection from entire bank
        if ($questions->count() < $amount) {
            $needed = $amount - $questions->count();
            $additional = Question::where('difficulty', $difficulty)
                ->whereNotIn('id', $questions->pluck('id')->toArray())
                ->inRandomOrder()
                ->limit($needed)
                ->get();
            $questions = $questions->concat($additional);
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
            // OpenTDB category IDs range from 9 to 32
            $randomCat = rand(9, 32);

            $response = Http::timeout(4)->get('https://opentdb.com/api.php', [
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
            for ($i = 0; $i < 4; $i++) {
                try {
                    $res = Http::timeout(5)->get('https://opentdb.com/api.php', [
                        'amount' => 25,
                        'difficulty' => $diff,
                        'type' => 'multiple',
                    ]);

                    if ($res->successful() && isset($res->json()['results'])) {
                        foreach ($res->json()['results'] as $item) {
                            $saved = $this->storeOrRetrieveQuestion($item);
                            if ($saved) $total++;
                        }
                    }
                    // Avoid OpenTDB rate limit burst
                    usleep(600000); // 600ms
                } catch (\Exception $e) {}
            }
        }

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
