<?php

function v($value) {
    if (is_null($value) || $value === '') return null; 
    if (is_array($value)) return json_encode($value, JSON_UNESCAPED_UNICODE);
    // if (is_numeric($value)) return (int) $value; 

    return trim((string) $value);
}

function distance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371000;
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLng / 2) * sin($dLng / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}

function formatDistance(float $meters): string {
    if ($meters < 1000) {
        return round($meters) . ' m';
    }
    return round($meters / 1000, 1) . ' km';
}