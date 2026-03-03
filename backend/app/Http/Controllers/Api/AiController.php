<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Deck;

class AiController extends Controller
{
    private function callGroq(string $systemPrompt, string $userPrompt): string
    {
        $response = Http::withoutVerifying()
            ->timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Groq API error: ' . $response->body());
        }

        return $response->json('choices.0.message.content', '');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:200',
            'num_cards' => 'integer|min:5|max:20',
            'difficulty' => 'in:beginner,intermediate,advanced',
        ]);

        $topic = $request->topic;
        $numCards = $request->num_cards ?? 10;
        $difficulty = $request->difficulty ?? 'intermediate';

        $difficultyGuide = match($difficulty) {
            'beginner' => 'Use simple language, basic concepts, and common examples.',
            'intermediate' => 'Assume basic knowledge, include nuance and practical examples.',
            'advanced' => 'Use technical language, edge cases, and deep concepts.',
        };

        $prompt = "Generate exactly {$numCards} flashcards about \"{$topic}\" at a {$difficulty} level.

Return ONLY a valid JSON array, no other text, no markdown, no code blocks, no explanation.
Format:
[
  {\"front\": \"Question or term here\", \"back\": \"Answer or definition here\"}
]

{$difficultyGuide}
Make cards educational, accurate, and progressively building on each other.";

        try {
            $text = $this->callGroq(
                'You are a flashcard generator. Always respond with only a valid JSON array, no other text.',
                $prompt
            );

            $clean = preg_replace('/```json|```/i', '', $text);
            $cards = json_decode(trim($clean), true);

            if (!$cards || !is_array($cards)) {
                return response()->json(['message' => 'Failed to parse AI response.'], 500);
            }

            return response()->json(['cards' => $cards]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'AI generation failed: ' . $e->getMessage()], 500);
        }
    }

    public function hint(Request $request)
    {
        $request->validate([
            'front' => 'required|string|max:500',
            'back'  => 'required|string|max:500',
        ]);

        $front = $request->front;
        $back  = $request->back;

        try {
            $hint = $this->callGroq(
                'You are a helpful study assistant. Give short, useful hints that guide students toward the answer without revealing it directly. Keep hints to 1-2 sentences max.',
                "A student is struggling with this flashcard and has failed it 3+ times.
Card front (question): {$front}
Card back (answer): {$back}

Give a helpful hint that nudges them toward the answer without giving it away. Be encouraging."
            );

            return response()->json(['hint' => trim($hint)]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to generate hint.'], 500);
        }
    }

    public function suggestions(Request $request)
    {
        $user = $request->user();

        // Get user's deck topics/tags
        $userDecks = Deck::where('user_id', $user->id)
            ->with('tags')
            ->latest()
            ->take(5)
            ->get();

        $topics = $userDecks->flatMap(fn($d) => $d->tags->pluck('name'))
            ->merge($userDecks->pluck('title'))
            ->unique()
            ->take(10)
            ->implode(', ');

        if (!$topics) {
            // No decks yet — return popular public decks
            $publicDecks = Deck::where('is_public', true)
                ->where('user_id', '!=', $user->id)
                ->withCount('cards')
                ->with('tags')
                ->inRandomOrder()
                ->take(3)
                ->get();

            return response()->json(['suggestions' => $publicDecks, 'reason' => 'Popular decks to get you started']);
        }

        // Get all public decks not owned by user
        $publicDecks = Deck::where('is_public', true)
            ->where('user_id', '!=', $user->id)
            ->withCount('cards')
            ->with('tags')
            ->get();

        if ($publicDecks->isEmpty()) {
            return response()->json(['suggestions' => [], 'reason' => '']);
        }

        $deckList = $publicDecks->map(fn($d) => [
            'id' => $d->id,
            'title' => $d->title,
            'tags' => $d->tags->pluck('name')->implode(', '),
        ])->toJson();

        try {
            $text = $this->callGroq(
                'You are a study recommendation engine. Always respond with only a valid JSON array of deck IDs, no other text.',
                "A student studies these topics: {$topics}

Available public decks (JSON):
{$deckList}

Return ONLY a JSON array of the 3 most relevant deck IDs for this student, ordered by relevance.
Example: [12, 5, 23]
Return only the array, nothing else."
            );

            $clean = preg_replace('/```json|```/i', '', $text);
            $ids = json_decode(trim($clean), true);

            if (!$ids || !is_array($ids)) {
                $suggested = $publicDecks->take(3);
            } else {
                $suggested = $publicDecks->whereIn('id', $ids)->take(3);
                if ($suggested->isEmpty()) $suggested = $publicDecks->take(3);
            }

            return response()->json([
                'suggestions' => $suggested->values(),
                'reason' => "Based on your interest in: " . $userDecks->pluck('title')->implode(', ')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'suggestions' => $publicDecks->take(3)->values(),
                'reason' => 'Popular decks you might like'
            ]);
        }
    }
}
