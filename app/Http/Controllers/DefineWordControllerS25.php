<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DefineWordControllerS25 extends Controller
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
        $cacheKey = 'dictionary_' . strtolower($query);

        try {
            $wordData = Cache::remember($cacheKey, now()->addHours(12), function () use ($query) {
                $response = Http::get("https://api.dictionaryapi.dev/api/v2/entries/en/{$query}");

                if (! $response->successful()) {
                    $json = $response->json();

                    if (isset($json['title']) && $json['title'] === 'No Definitions Found') {
                        throw new Exception('Word not found', 422);
                    }

                    throw new Exception('Failed to fetch definition', 500);
                }

                return $response->json();
            });
        } catch (\Exception $e) {
            $status = $e->getCode() === 422 ? 422 : 500;
            $message = $e->getCode() === 422 ? 'Word not found' : 'Server error: ' . $e->getMessage();

            return response()->json([
                'message' => $message,
            ], $status);
        }

        return response()->json([
            'word' => $wordData[0]['word'] ?? '',
            'phonetic' => $wordData[0]['phonetic'] ?? '',
            'definition' => $wordData[0]['meanings'][0]['definitions'][0]['definition'] ?? ''
        ]);
    }

    public function wordTrap(Request $request): JsonResponse
    {
        if(! $request->filled('word')) {
            return response()->json([
                'message' => 'Word not provided',
            ], 422);
        }

        $query = $request->input('word');
        $cacheKey = 'dictionary_trap_' . strtolower($query);

        try {
            $wordData = Cache::remember($cacheKey, now()->addHours(12), function () use ($query) {
                $response = Http::get("https://api.dictionaryapi.dev/api/v2/entries/en/{$query}");

                if (! $response->successful()) {
                    $json = $response->json();

                    if (isset($json['title']) && $json['title'] === 'No Definitions Found') {
                        throw new Exception('Word not found', 422);
                    }

                    throw new Exception('Failed to fetch definition', 500);
                }

                return $response->json();
            });
        } catch (\Exception $e) {
            $status = $e->getCode() === 422 ? 422 : 500;
            $message = $e->getCode() === 422 ? 'Word not found' : 'Server error: ' . $e->getMessage();

            return response()->json([
                'message' => $message,
            ], $status);
        }

        return response()->json([
            'word' => $wordData[0]['word'] ?? '',
            'phonetic' => $wordData[0]['phonetic'] ?? '',
            'definition' => $wordData[0]['meanings'][0]['definitions'][0]['definition'] ?? ''
        ]);
    }
}
