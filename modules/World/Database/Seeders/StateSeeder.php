<?php

namespace Modules\World\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\World\Models\State;
use Modules\World\Models\Country;

class StateSeeder extends Seeder {
    public function run(): void {
        $states = [
            ["name" => "Adrar",           'iso2' => '01', 'lat' => 27.8742, 'lng' => -0.2936, 'country_iso2' => 'DZ'],
            ["name" => "Chlef",           'iso2' => '02', 'lat' => 36.1657, 'lng' => 1.3313,  'country_iso2' => 'DZ'],
            ["name" => "Laghouat",        'iso2' => '03', 'lat' => 33.8070, 'lng' => 2.8783,  'country_iso2' => 'DZ'],
            ["name" => "Oum El Bouaghi",  'iso2' => '04', 'lat' => 35.8759, 'lng' => 7.1136,  'country_iso2' => 'DZ'],
            ["name" => "Batna",           'iso2' => '05', 'lat' => 35.5550, 'lng' => 6.1739,  'country_iso2' => 'DZ'],
            ["name" => "Béjaïa",          'iso2' => '06', 'lat' => 36.7500, 'lng' => 5.0567,  'country_iso2' => 'DZ'],
            ["name" => "Biskra",          'iso2' => '07', 'lat' => 34.8516, 'lng' => 5.7333,  'country_iso2' => 'DZ'],
            ["name" => "Béchar",          'iso2' => '08', 'lat' => 31.6167, 'lng' => -2.2167, 'country_iso2' => 'DZ'],
            ["name" => "Blida",           'iso2' => '09', 'lat' => 36.4731, 'lng' => 2.8290,  'country_iso2' => 'DZ'],
            ["name" => "Bouira",          'iso2' => '10', 'lat' => 36.3750, 'lng' => 3.9000,  'country_iso2' => 'DZ'],
            ["name" => "Tamanrasset",     'iso2' => '11', 'lat' => 22.7850, 'lng' => 5.5228,  'country_iso2' => 'DZ'],
            ["name" => "Tébessa",         'iso2' => '12', 'lat' => 35.4042, 'lng' => 8.1247,  'country_iso2' => 'DZ'],
            ["name" => "Tlemcen",         'iso2' => '13', 'lat' => 34.8783, 'lng' => -1.3156, 'country_iso2' => 'DZ'],
            ["name" => "Tiaret",          'iso2' => '14', 'lat' => 35.3717, 'lng' => 1.3222,  'country_iso2' => 'DZ'],
            ["name" => "Tizi Ouzou",      'iso2' => '15', 'lat' => 36.7167, 'lng' => 4.0500,  'country_iso2' => 'DZ'],
            ["name" => "Algiers",         'iso2' => '16', 'lat' => 36.7538, 'lng' => 3.0588,  'country_iso2' => 'DZ'],
            ["name" => "Djelfa",          'iso2' => '17', 'lat' => 34.6667, 'lng' => 3.2500,  'country_iso2' => 'DZ'],
            ["name" => "Jijel",           'iso2' => '18', 'lat' => 36.8200, 'lng' => 5.7667,  'country_iso2' => 'DZ'],
            ["name" => "Sétif",           'iso2' => '19', 'lat' => 36.1900, 'lng' => 5.4100,  'country_iso2' => 'DZ'],
            ["name" => "Saïda",           'iso2' => '20', 'lat' => 34.8300, 'lng' => 0.1500,  'country_iso2' => 'DZ'],
            ["name" => "Skikda",          'iso2' => '21', 'lat' => 36.8667, 'lng' => 6.9000,  'country_iso2' => 'DZ'],
            ["name" => "Sidi Bel Abbès",  'iso2' => '22', 'lat' => 35.2000, 'lng' => -0.6333, 'country_iso2' => 'DZ'],
            ["name" => "Annaba",          'iso2' => '23', 'lat' => 36.9000, 'lng' => 7.7667,  'country_iso2' => 'DZ'],
            ["name" => "Guelma",          'iso2' => '24', 'lat' => 36.4667, 'lng' => 7.4333,  'country_iso2' => 'DZ'],
            ["name" => "Constantine",     'iso2' => '25', 'lat' => 36.3650, 'lng' => 6.6147,  'country_iso2' => 'DZ'],
            ["name" => "Médéa",           'iso2' => '26', 'lat' => 36.2647, 'lng' => 2.7500,  'country_iso2' => 'DZ'],
            ["name" => "Mostaganem",      'iso2' => '27', 'lat' => 35.9333, 'lng' => 0.0897,  'country_iso2' => 'DZ'],
            ["name" => "M'Sila",          'iso2' => '28', 'lat' => 35.7000, 'lng' => 4.5333,  'country_iso2' => 'DZ'],
            ["name" => "Mascara",         'iso2' => '29', 'lat' => 35.4000, 'lng' => 0.1400,  'country_iso2' => 'DZ'],
            ["name" => "Ouargla",         'iso2' => '30', 'lat' => 31.9500, 'lng' => 5.3333,  'country_iso2' => 'DZ'],
            ["name" => "Oran",            'iso2' => '31', 'lat' => 35.6997, 'lng' => -0.6350, 'country_iso2' => 'DZ'],
            ["name" => "El Bayadh",       'iso2' => '32', 'lat' => 33.6833, 'lng' => 1.0167,  'country_iso2' => 'DZ'],
            ["name" => "Illizi",          'iso2' => '33', 'lat' => 26.5000, 'lng' => 8.4667,  'country_iso2' => 'DZ'],
            ["name" => "Bordj Bou Arreridj", 'iso2' => '34', 'lat' => 36.0667, 'lng' => 4.7667, 'country_iso2' => 'DZ'],
            ["name" => "Boumerdès",       'iso2' => '35', 'lat' => 36.7667, 'lng' => 3.5000,  'country_iso2' => 'DZ'],
            ["name" => "El Tarf",         'iso2' => '36', 'lat' => 36.7672, 'lng' => 8.3000,  'country_iso2' => 'DZ'],
            ["name" => "Tindouf",         'iso2' => '37', 'lat' => 27.6761, 'lng' => -8.1333, 'country_iso2' => 'DZ'],
            ["name" => "Tissemsilt",      'iso2' => '38', 'lat' => 35.6000, 'lng' => 1.8167,  'country_iso2' => 'DZ'],
            ["name" => "El Oued",         'iso2' => '39', 'lat' => 33.3683, 'lng' => 6.8678,  'country_iso2' => 'DZ'],
            ["name" => "Khenchela",       'iso2' => '40', 'lat' => 35.4333, 'lng' => 7.1333,  'country_iso2' => 'DZ'],
            ["name" => "Souk Ahras",      'iso2' => '41', 'lat' => 36.2861, 'lng' => 7.9519,  'country_iso2' => 'DZ'],
            ["name" => "Tipaza",          'iso2' => '42', 'lat' => 36.5900, 'lng' => 2.4500,  'country_iso2' => 'DZ'],
            ["name" => "Mila",            'iso2' => '43', 'lat' => 36.4500, 'lng' => 6.2667,  'country_iso2' => 'DZ'],
            ["name" => "Aïn Defla",       'iso2' => '44', 'lat' => 36.2500, 'lng' => 1.9667,  'country_iso2' => 'DZ'],
            ["name" => "Naama",           'iso2' => '45', 'lat' => 33.2667, 'lng' => -0.2667, 'country_iso2' => 'DZ'],
            ["name" => "Aïn Témouchent",  'iso2' => '46', 'lat' => 35.2961, 'lng' => -1.1406, 'country_iso2' => 'DZ'],
            ["name" => "Ghardaïa",        'iso2' => '47', 'lat' => 32.4900, 'lng' => 3.6700,  'country_iso2' => 'DZ'],
            ["name" => "Relizane",        'iso2' => '48', 'lat' => 35.7378, 'lng' => 0.5556,  'country_iso2' => 'DZ'],
            ["name" => "El M'ghair",      'iso2' => '49', 'lat' => 33.958070, 'lng' => 5.701126, 'country_iso2' => 'DZ'],
            ["name" => "El Menia",        'iso2' => '50', 'lat' => 30.707189, 'lng' => 3.048063, 'country_iso2' => 'DZ'],
            ["name" => "Ouled Djellal",   'iso2' => '51', 'lat' => 34.425410, 'lng' => 5.064434, 'country_iso2' => 'DZ'],
            ["name" => "Bordj Baji Mokhtar", 'iso2' => '52', 'lat' => 22.626146, 'lng' => 0.127582, 'country_iso2' => 'DZ'],
            ["name" => "Béni Abbès",      'iso2' => '53', 'lat' => 30.108921, 'lng' => -2.462255, 'country_iso2' => 'DZ'],
            ["name" => "Timimoun",        'iso2' => '54', 'lat' => 30.024473, 'lng' => 0.848598, 'country_iso2' => 'DZ'],
            ["name" => "Touggourt",       'iso2' => '55', 'lat' => 33.104857, 'lng' => 6.066728, 'country_iso2' => 'DZ'],
            ["name" => "Djanet",          'iso2' => '56', 'lat' => 24.071521, 'lng' => 9.615585, 'country_iso2' => 'DZ'],
            ["name" => "In Salah",        'iso2' => '57', 'lat' => 27.195033, 'lng' => 2.482613, 'country_iso2' => 'DZ'],
            ["name" => "In Guezzam",      'iso2' => '58', 'lat' => 20.427530, 'lng' => 4.676631, 'country_iso2' => 'DZ'],
        ];


        foreach ($states as $s) {
            $country = Country::where('iso2', $s['country_iso2'])->first();
            if ($country) {
                State::firstOrCreate(
                    ['name' => $s['name'], 'country_id' => $country->id],
                    ['iso2' => $s['iso2'], 'lat' => $s['lat'], 'lng' => $s['lng']]
                );
            }
        }
    }
}
