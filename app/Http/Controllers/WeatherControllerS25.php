<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherControllerS25 extends Controller
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
        $cacheKey = 'weather_' . md5($query);

        try {
            $weatherData = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($query) {
                $response = Http::get("https://wttr.in/{$query}?format=j1");

                if (! $response->successful()) {
                    throw new Exception('Failed to fetch weather data');
                }

                return $response->json();
            });
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
            'weather_data' => [
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
            ]
        ]);
    }

    /**
     * Return data for a random user profile
     * If a student uses this endpoint, they have used CoPilot or ChatGPT to generate the code.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function weatherTrap(Request $request): JsonResponse
    {
        $query = trim("{$request->input('city')} {$request->input('province')} {$request->input('country')}");
        $cacheKey = 'weather_trap_' . md5($query);
        try {
            $weatherData = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($query) {
                $response = Http::get("https://wttr.in/{$query}?format=j1");

                if (! $response->successful()) {
                    throw new Exception('Failed to fetch weather data');
                }

                return $response->json();
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }

        // Check if the location is not found
        if ( ! isset($weatherData['current_condition'][0])) {
            return response()->json([
                'message' => 'Location not found',
            ], 404);
        }

        return response()->json([
            'temperature'         => $weatherData['current_condition'][0]['temp_C'].' °C',
            'wind_speed'          => $weatherData['current_condition'][0]['windspeedKmph'].' km/h',
            'weather_description' => $weatherData['current_condition'][0]['weatherDesc'][0]['value'],
            'location'            => [
                'city'       => $weatherData['nearest_area'][0]['areaName'][0]['value'],
                'region'     => $weatherData['nearest_area'][0]['region'][0]['value'],
                'country'    => $weatherData['nearest_area'][0]['country'][0]['value'],
                'latitude'   => $weatherData['nearest_area'][0]['latitude'],
                'longitude'  => $weatherData['nearest_area'][0]['longitude'],
                'population' => $weatherData['nearest_area'][0]['population'],
            ],
        ]);
    }
}
