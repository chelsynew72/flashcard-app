<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deck;
use App\Models\Tag;
use Illuminate\Http\Request;

class DeckController extends Controller
{
    public function index(Request $request)
    {
        $decks = Deck::where('user_id', $request->user()->id)
            ->with('tags')
            ->withCount('cards')
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->tag, fn($q) => $q->whereHas('tags', fn($q) => $q->where('name', $request->tag)))
            ->latest()
            ->get();

        return response()->json($decks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public'   => 'boolean',
            'tags'        => 'nullable|array',
            'tags.*'      => 'string|max:50',
        ]);

        $deck = Deck::create([
            'user_id'     => $request->user()->id,
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_public'   => $validated['is_public'] ?? false,
        ]);

        if (!empty($validated['tags'])) {
            $tagIds = collect($validated['tags'])->map(function ($name) {
                return Tag::firstOrCreate(['name' => strtolower($name)])->id;
            });
            $deck->tags()->sync($tagIds);
        }

        return response()->json($deck->load('tags'), 201);
    }

    public function show(Request $request, $id)
    {
        $deck = Deck::where('user_id', $request->user()->id)
            ->with(['tags', 'cards'])
            ->withCount('cards')
            ->findOrFail($id);

        return response()->json($deck);
    }

    public function update(Request $request, $id)
    {
        $deck = Deck::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_public'   => 'boolean',
            'tags'        => 'nullable|array',
            'tags.*'      => 'string|max:50',
        ]);

        $deck->update($validated);

        if (isset($validated['tags'])) {
            $tagIds = collect($validated['tags'])->map(function ($name) {
                return Tag::firstOrCreate(['name' => strtolower($name)])->id;
            });
            $deck->tags()->sync($tagIds);
        }

        return response()->json($deck->load('tags'));
    }

    public function destroy(Request $request, $id)
    {
        $deck = Deck::where('user_id', $request->user()->id)->findOrFail($id);
        $deck->delete();

        return response()->json(['message' => 'Deck deleted successfully']);
    }

    public function clone(Request $request, $id)
    {
        $original = Deck::where('is_public', true)->with('cards', 'tags')->findOrFail($id);

        $newDeck = Deck::create([
            'user_id'     => $request->user()->id,
            'title'       => $original->title . ' (copy)',
            'description' => $original->description,
            'is_public'   => false,
        ]);

        foreach ($original->cards as $card) {
            $newDeck->cards()->create([
                'front' => $card->front,
                'back'  => $card->back,
            ]);
        }

        $newDeck->tags()->sync($original->tags->pluck('id'));

        return response()->json($newDeck->load('tags', 'cards'), 201);
    }
}
