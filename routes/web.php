<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Quiwin Interactive Quiz System
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        /** @var User $user */
        $user = auth()->user();
        return $user->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('user.home');
    }
    return redirect()->route('login');
});

// Guest Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Player Dashboard & Actions
    Route::get('/home', [UserController::class, 'home'])->name('user.home');
    Route::post('/user/top-up', [UserController::class, 'topUp'])->name('user.topup');
    Route::post('/user/withdraw', [UserController::class, 'withdraw'])->name('user.withdraw');
    Route::post('/user/rank-reward/claim', [UserController::class, 'claimRankReward'])->name('user.rankreward.claim');
    Route::post('/user/mail/{mailId}/read', [UserController::class, 'markMailRead'])->name('user.mail.read');
    Route::post('/user/mail/read-all', [UserController::class, 'markAllMailsRead'])->name('user.mail.readall');

    // Game Arena Routes
    Route::post('/game/start', [GameController::class, 'start'])->name('game.start');
    Route::get('/game/{sessionId}/play', [GameController::class, 'play'])->name('game.play');
    Route::get('/game/{sessionId}/state', [GameController::class, 'getState'])->name('game.state');
    Route::post('/game/{sessionId}/answer', [GameController::class, 'submitAnswer'])->name('game.answer');
    Route::post('/game/{sessionId}/top-up', [GameController::class, 'topUpInGame'])->name('game.topup');
    Route::get('/game/{sessionId}/summary', [GameController::class, 'summary'])->name('game.summary');
    Route::post('/game/{sessionId}/abandon', [GameController::class, 'abandon'])->name('game.abandon');

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{userId}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
        Route::post('/users/{userId}/reject', [AdminController::class, 'rejectUser'])->name('users.reject');
        Route::post('/users/{userId}/points', [AdminController::class, 'updateUserPoints'])->name('users.points');
        Route::post('/users/{userId}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
        Route::get('/withdrawals', [AdminController::class, 'withdrawals'])->name('withdrawals');
        Route::post('/withdrawals/{id}/approve', [AdminController::class, 'approveWithdrawal'])->name('withdrawals.approve');
        Route::post('/withdrawals/{id}/reject', [AdminController::class, 'rejectWithdrawal'])->name('withdrawals.reject');
        Route::get('/questions', [AdminController::class, 'questions'])->name('questions');
        Route::post('/questions/sync', [AdminController::class, 'syncQuestions'])->name('questions.sync');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/reset', [AdminController::class, 'resetSettings'])->name('settings.reset');
    });
});
