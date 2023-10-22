<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' =>  'paulo_martins@mail.pt'],
            [
                'name' => 'Paulo Martins',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'name' => 'Paulo Martins',
                // 'remember_token' => Str::random(10),
            ]
        );
    }
}
