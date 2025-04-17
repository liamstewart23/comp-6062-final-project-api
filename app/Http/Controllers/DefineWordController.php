<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        try {
            $response = Http::get("https://api.dictionaryapi.dev/api/v2/entries/en/{$query}");
            if (! $response->successful()) {

                if ($response->json()['title'] === 'No Definitions Found') {
                    return response()->json([
                        'message' => 'Word not found',
                    ], 422);
                }

                return response()->json([
                    'message' => 'Server error',
                ], 500);
            }

            $wordData = $response->json();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }

        $data[0] = [
            'word' => $wordData[0]['word'] ?? '',
            'phonetic' => $wordData[0]['phonetic'] ?? '',
            'definition' => $wordData[0]['meanings'][0]['definitions'][0]['definition'] ?? ''
        ];

        return response()->json($data);
    }
}
