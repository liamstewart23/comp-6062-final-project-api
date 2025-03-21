<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RandomUserProfileController extends Controller
{
    /**
     * Return data for a random user profile
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $response = Http::get('https://randomuser.me/api/');
            $data = $response->json();
        } catch (ConnectionException $e) {
            return response()->json([
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }

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
