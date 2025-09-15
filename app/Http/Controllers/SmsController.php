<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller {

    public static function sendSms($destination, $message) {
        $host = 'sms.icosnet.com';
        $port = '8080';
        $username = 'TRANSTEV';
        $password = '006833';
        $source = 'ICOTEST';
        $type = '0';
        $dlr = '1';

        $url = "http://$host:$port/bulksms/bulksms?username=$username&password=$password&type=$type&dlr=$dlr&destination=$destination&source=$source&message=$message";

        try {
            $proxyUrl = "https://odiro-dz.com/tfa/public/url";
            $response = Http::get($proxyUrl, ['url' => $url]);
            return $response;
            Http::get($url, $params);
        } catch (Exception $e) {
            Log::error('Failed to send OTP SMS', [
                'mobile'   => $mobile ?? null,
                'message'   => $message ?? null,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }
    }
}
