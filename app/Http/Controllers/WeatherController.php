<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    /**
     * Display the weather information utilizing this API: https://wttr.in/Toronto
     * for temperature, wind speed, description, and with the bonus add of location (city, region, country, latitude, longitude, population)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if(! $request->filled('city')) {
            return response()->json([
                'message' => 'City not provided',
            ], 422);
        }

        if(! $request->filled('province')) {
            return response()->json([
                'message' => 'Province not provided',
            ], 422);
        }

        if(! $request->filled('country')) {
            return response()->json([
                'message' => 'Country not provided',
            ], 422);
        }

        $query = trim("{$request->input('city')} {$request->input('province')} {$request->input('country')}");

        try {
            $response = Http::get("https://wttr.in/{$query}?format=j1");
            if (! $response->successful()) {
                return response()->json([
                    'message' => 'Server error',
                ], 500);
            }

            $weatherData =  $response->json();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }

        // Check if the location is not found
        if (!isset($weatherData['current_condition'][0])) {
            return response()->json([
                'message' => 'Location not found',
            ], 404);
        }

        return response()->json([
            'temperature' => $weatherData['current_condition'][0]['temp_C'] . ' °C',
            'wind_speed' => $weatherData['current_condition'][0]['windspeedKmph'] . ' km/h',
            'weather_description' => $weatherData['current_condition'][0]['weatherDesc'][0]['value'],
            'location' => [
                'city' => $weatherData['nearest_area'][0]['areaName'][0]['value'],
                'region' => $weatherData['nearest_area'][0]['region'][0]['value'],
                'country' => $weatherData['nearest_area'][0]['country'][0]['value'],
                'latitude' => $weatherData['nearest_area'][0]['latitude'],
                'longitude' => $weatherData['nearest_area'][0]['longitude'],
                'population' => $weatherData['nearest_area'][0]['population'],
            ],
        ]);
    }
}
