<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeckController;
use App\Http\Controllers\Api\ExploreController;
use App\Http\Controllers\Api\StudySessionController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GoogleAuthController;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/register',        [AuthController::class, 'register']);
        Route::post('/login',           [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password',  [AuthController::class, 'resetPassword']);
        Route::get('/google',          [GoogleAuthController::class, 'redirect']);
Route::get('/google/callback', [GoogleAuthController::class, 'callback']);
    });

    Route::get('/explore',      [ExploreController::class, 'index']);
    Route::get('/explore/{id}', [ExploreController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/user',             [UserController::class, 'profile']);
        Route::put('/user/profile',     [UserController::class, 'updateProfile']);
        Route::put('/user/password',    [UserController::class, 'updatePassword']);
        Route::put('/user/preferences', [UserController::class, 'updatePreferences']);
        Route::delete('/user',          [UserController::class, 'destroy']);
        Route::get('/tags', [TagController::class, 'index']);
        Route::apiResource('decks', DeckController::class);
        Route::post('/decks/{id}/clone', [DeckController::class, 'clone']);
        Route::get('/decks/{deckId}/cards',             [CardController::class, 'index']);
        Route::post('/decks/{deckId}/cards',            [CardController::class, 'store']);
        Route::put('/decks/{deckId}/cards/{cardId}',    [CardController::class, 'update']);
        Route::delete('/decks/{deckId}/cards/{cardId}', [CardController::class, 'destroy']);
        Route::post('/decks/{deckId}/cards/import',     [CardController::class, 'import']);
        Route::post('/decks/{deckId}/study/start',    [StudySessionController::class, 'start']);
        Route::get('/sessions/{sessionId}/next-card', [StudySessionController::class, 'nextCard']);
        Route::post('/sessions/{sessionId}/rate',     [StudySessionController::class, 'rate']);
        Route::post('/sessions/{sessionId}/complete', [StudySessionController::class, 'complete']);

        // AI
        Route::post('/ai/generate',     [AiController::class, 'generate']);
        Route::post('/ai/hint',         [AiController::class, 'hint']);
        Route::get('/ai/suggestions',   [AiController::class, 'suggestions']);
    });
});
