<?php

// app/Services/GoogleMapsService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleMapsService {
    /**
     * Get driving directions from source to destination.
     *
     * @param array $source ['lat' => float, 'lng' => float]
     * @param array $destination ['lat' => float, 'lng' => float]
     * @return array|null
     */
    public static function getDirections(array $source, array $destination) {
        $key = env('GOOGLE_MAPS_KEY');

        $response = Http::get("https://maps.googleapis.com/maps/api/directions/json", [
            'origin' => "{$source['lat']},{$source['lng']}",
            'destination' => "{$destination['lat']},{$destination['lng']}",
            'mode' => 'driving',
            'key' => $key,
        ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        if (empty($data['routes'])) {
            return null;
        }

        $route = $data['routes'][0];
        $leg   = $route['legs'][0];

        return [
            'distance' => $leg['distance']['text'],
            'duration' => $leg['duration']['text'],
            'polyline' => $route['overview_polyline']['points'],
            'steps'    => collect($leg['steps'])->map(function ($step) {
                return [
                    'instruction' => strip_tags($step['html_instructions']),
                    'distance'    => $step['distance']['text'],
                    'duration'    => $step['duration']['text'],
                    'start'       => $step['start_location'],
                    'end'         => $step['end_location'],
                ];
            })->toArray(),
        ];
    }
}
