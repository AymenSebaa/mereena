<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Hotel;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;


class FlightController extends Controller {

    public static function fetch() {
        $lang = 'en';
        $url = "https://iatf.aeroportalger.dz/api/flights/{$lang}/terminal-to";

        $proxyUrl = "https://odiro-dz.com/tfa/public/url";
        $response = Http::get($proxyUrl, [
            'url' => $url
        ]);

        if (!$response->ok()) {
            return back()->with('error', 'Failed to fetch flights');
        }

        $flights = collect($response->json() ?? []);

        // Filter out past flights
        $now = Carbon::now();
        $data['flights'] = $flights->filter(function ($flight) use ($now) {
            $datetime = Carbon::parse($flight['operationTime']['date'] . ' ' . $flight['operationTime']['time'] . ':00');
            return $datetime->greaterThanOrEqualTo($now);
        });

        // Separate for clarity if you want two sections
        $data['departures'] = $data['flights']->where('departureOrArrival', 'departure');
        $data['arrivals']   = $data['flights']->where('departureOrArrival', 'arrival');

        return $data;
    }

    public function index() {
        $data = $this->fetch();

        return view('flights.index', $data);
    }

    public function book(Request $request) {
        $data['countries'] = Country::orderBy('name_en')->get();
        $data['hotels'] = Hotel::orderBy('name')->get();
        $data['categories'] = Type::where('name', 'Guest categories')->first()->subTypes;
        $data['departures'] = $this->fetch()['departures'];

        return view('flights.book', $data);
    }

}
