<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardProgress;
use App\Models\Deck;
use App\Models\StudySession;
use App\Services\SpacedRepetitionService;
use Illuminate\Http\Request;

class StudySessionController extends Controller
{
    public function start(Request $request, $deckId)
    {
        $deck = Deck::where('user_id', $request->user()->id)->findOrFail($deckId);

        $session = StudySession::create([
            'user_id' => $request->user()->id,
            'deck_id' => $deck->id,
        ]);

        $dueCards = $this->getDueCards($request->user()->id, $deck->id);

        return response()->json([
            'session_id'  => $session->id,
            'deck_title'  => $deck->title,
            'total_cards' => $dueCards->count(),
        ]);
    }

    public function nextCard(Request $request, $sessionId)
    {
        $session = StudySession::where('user_id', $request->user()->id)->findOrFail($sessionId);
        $dueCards = $this->getDueCards($request->user()->id, $session->deck_id);

        if ($dueCards->isEmpty()) {
            return response()->json(['message' => 'No more cards due', 'finished' => true]);
        }

        $card = $dueCards->first();

        return response()->json([
            'finished'    => false,
            'card'        => $card,
            'remaining'   => $dueCards->count(),
        ]);
    }

    public function rate(Request $request, $sessionId)
    {
        $request->validate([
            'card_id' => 'required|exists:cards,id',
            'rating'  => 'required|in:again,hard,good,easy',
        ]);

        $session = StudySession::where('user_id', $request->user()->id)->findOrFail($sessionId);

        $progress = CardProgress::firstOrNew([
            'user_id' => $request->user()->id,
            'card_id' => $request->card_id,
        ]);

        if (!$progress->exists) {
            $progress->easiness_factor = 2.5;
            $progress->repetitions     = 0;
            $progress->interval        = 1;
        }

        $srs    = new SpacedRepetitionService();
        $result = $srs->calculate($progress->toArray(), $request->rating);

        $progress->fill($result);
        $progress->last_reviewed_at = now();
        $progress->last_rating      = $request->rating;
        $progress->save();

        // Update session counts
        $session->increment('cards_studied');
        $session->increment($request->rating . '_count');

        return response()->json([
            'next_review_at' => $result['next_review_at'],
            'interval_days'  => $result['interval'],
        ]);
    }

    public function complete(Request $request, $sessionId)
    {
        $session = StudySession::where('user_id', $request->user()->id)->findOrFail($sessionId);
        $session->update(['completed_at' => now()]);

        // Update user streak
        $user = $request->user();
        $lastStudied = $user->last_studied_at;
        $today = now()->toDateString();

        if ($lastStudied === null || $lastStudied < today()->subDay()->toDateString()) {
            $user->study_streak = 1;
        } elseif ($lastStudied === today()->subDay()->toDateString()) {
            $user->study_streak++;
        }

        $user->last_studied_at = $today;
        $user->save();

        return response()->json([
            'message'       => 'Session complete!',
            'cards_studied' => $session->cards_studied,
            'again_count'   => $session->again_count,
            'hard_count'    => $session->hard_count,
            'good_count'    => $session->good_count,
            'easy_count'    => $session->easy_count,
            'streak'        => $user->study_streak,
        ]);
    }

    private function getDueCards($userId, $deckId)
    {
        return Card::where('deck_id', $deckId)
            ->where(function ($q) use ($userId) {
                $q->whereDoesntHave('progress', fn($q) => $q->where('user_id', $userId))
                  ->orWhereHas('progress', fn($q) => $q->where('user_id', $userId)
                      ->where('next_review_at', '<=', now()));
            })
            ->get();
    }
}
