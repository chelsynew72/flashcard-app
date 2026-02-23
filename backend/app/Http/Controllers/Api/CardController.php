<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deck;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index(Request $request, $deckId)
    {
        $deck = Deck::where('user_id', $request->user()->id)->findOrFail($deckId);

        return response()->json($deck->cards);
    }

    public function store(Request $request, $deckId)
    {
        $deck = Deck::where('user_id', $request->user()->id)->findOrFail($deckId);

        $validated = $request->validate([
            'front' => 'required|string',
            'back'  => 'required|string',
        ]);

        $card = $deck->cards()->create($validated);

        return response()->json($card, 201);
    }

    public function update(Request $request, $deckId, $cardId)
    {
        $deck = Deck::where('user_id', $request->user()->id)->findOrFail($deckId);
        $card = $deck->cards()->findOrFail($cardId);

        $validated = $request->validate([
            'front' => 'sometimes|string',
            'back'  => 'sometimes|string',
        ]);

        $card->update($validated);

        return response()->json($card);
    }

    public function destroy(Request $request, $deckId, $cardId)
    {
        $deck = Deck::where('user_id', $request->user()->id)->findOrFail($deckId);
        $card = $deck->cards()->findOrFail($cardId);
        $card->delete();

        return response()->json(['message' => 'Card deleted successfully']);
    }

    public function import(Request $request, $deckId)
    {
        $deck = Deck::where('user_id', $request->user()->id)->findOrFail($deckId);

        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file  = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        $cards = [];
        $row   = 0;

        while (($line = fgetcsv($handle)) !== false) {
            if ($row === 0) { $row++; continue; } // skip header
            if (count($line) >= 2) {
                $cards[] = $deck->cards()->create([
                    'front' => trim($line[0]),
                    'back'  => trim($line[1]),
                ]);
            }
            $row++;
        }

        fclose($handle);

        return response()->json([
            'message' => count($cards) . ' cards imported successfully',
            'cards'   => $cards,
        ], 201);
    }
}
