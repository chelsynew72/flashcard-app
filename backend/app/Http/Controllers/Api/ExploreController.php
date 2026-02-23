<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deck;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $decks = Deck::where('is_public', true)
            ->with(['tags', 'user:id,name'])
            ->withCount('cards')
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->tag, fn($q) => $q->whereHas('tags', fn($q) => $q->where('name', $request->tag)))
            ->latest()
            ->paginate(20);

        return response()->json($decks);
    }

    public function show($id)
    {
        $deck = Deck::where('is_public', true)
            ->with(['tags', 'cards', 'user:id,name'])
            ->withCount('cards')
            ->findOrFail($id);

        return response()->json($deck);
    }
}
