<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RandomUserProfileController extends Controller
{
    /**
     * Return data for a random user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $response = Http::get('https://randomuser.me/api/');
        if (! $response->successful()) {
            return response()->json([
                'message' => 'Server error',
            ], 500);
        }

        $data = $response->json();

        $profile = [
            'first_name' => $data['results'][0]['name']['first'] ?? '',
            'last_name' => $data['results'][0]['name']['last'] ?? '',
            'email' => $data['results'][0]['email'] ?? '',
            'age' => $data['results'][0]['dob']['age'] ?? '',
            'profile_picture' => $data['results'][0]['picture']['large'] ?? '',
        ];

        return response()->json($profile);
    }
}
