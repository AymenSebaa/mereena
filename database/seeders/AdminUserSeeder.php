<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminUserSeeder extends Seeder {
    public function run(): void {
        // update these if you want
        $email = env('ADMIN_EMAIL', 'admin@transfer.local');
        $password = env('ADMIN_PASSWORD', 'password'); // change in .env ASAP

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'PCC Admin',
                'email_verified_at' => now(),
                'password' => Hash::make($password),
                'remember_token' => Str::random(10),
            ]
        );
    }
}
