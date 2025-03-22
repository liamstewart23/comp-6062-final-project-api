<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DefineWordController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if(! $request->filled('word')) {
            return response()->json([
                'message' => 'Word not provided',
            ], 422);
        }

        $query = $request->input('word');
        $cacheKey = "word_data_{$query}";
        try {
            $wordData = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($query) {
                $response = Http::get("https://api.dictionaryapi.dev/api/v2/entries/en/{$query}");
                if (! $response->successful()) {
                    return response()->json([
                        'message' => 'Server error',
                    ], 500);
                }

                return $response->json();
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }

        $word = [
            'word' => $wordData[0]['word'] ?? '',
            'definition' => $wordData[0]['definition'] ?? '',
            'part_of_speech' => $wordData[0]['partOfSpeech'] ?? '',
            'synonyms' => $wordData[0]['synonyms'] ?? '',
            'antonyms' => $wordData[0]['antonyms'] ?? '',
        ];

        return response()->json($word);
    }
}
