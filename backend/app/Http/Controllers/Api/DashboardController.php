<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardProgress;
use App\Models\Deck;
use App\Models\StudySession;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $user   = $request->user();

        // Cards due today across all decks
        $cardsDueToday = CardProgress::where('user_id', $userId)
            ->where('next_review_at', '<=', now())
            ->count();

        // New cards (never studied) across all decks
        $totalCards = Card::whereHas('deck', fn($q) => $q->where('user_id', $userId))->count();

        $studiedCardIds = CardProgress::where('user_id', $userId)->pluck('card_id');

        $newCards = Card::whereHas('deck', fn($q) => $q->where('user_id', $userId))
            ->whereNotIn('id', $studiedCardIds)
            ->count();

        $cardsDueToday += $newCards;

        // Mastered cards (interval >= 21 days)
        $cardsMastered = CardProgress::where('user_id', $userId)
            ->where('interval', '>=', 21)
            ->count();

        // Weekly activity
        $weeklyActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $count = StudySession::where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->sum('cards_studied');
            $weeklyActivity[] = ['date' => $date, 'cards_studied' => $count];
        }

        // Recent decks
        $recentDecks = Deck::where('user_id', $userId)
            ->withCount('cards')
            ->with('tags')
            ->latest()
            ->take(3)
            ->get();

        // Decks with cards due today
        $decksDueToday = Deck::where('user_id', $userId)
            ->whereHas('cards', function ($q) use ($userId) {
                $q->where(function ($q) use ($userId) {
                    $q->whereDoesntHave('progress', fn($q) => $q->where('user_id', $userId))
                      ->orWhereHas('progress', fn($q) => $q->where('user_id', $userId)
                          ->where('next_review_at', '<=', now()));
                });
            })
            ->withCount('cards')
            ->get();

        return response()->json([
            'cards_due_today'  => $cardsDueToday,
            'study_streak'     => $user->study_streak,
            'total_decks'      => Deck::where('user_id', $userId)->count(),
            'total_cards'      => $totalCards,
            'cards_learned'    => $studiedCardIds->count(),
            'cards_mastered'   => $cardsMastered,
            'weekly_activity'  => $weeklyActivity,
            'recent_decks'     => $recentDecks,
            'decks_due_today'  => $decksDueToday,
        ]);
    }
}
