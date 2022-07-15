<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $su = User::create([
            'name' => 'root',
            'username' => 'su',
            'email' => 'root@local',
            'password' => $password = Hash::make('password'),
        ]);

        $su->email_verified_at = now();
        $su->save();

        $user = User::create([
            'name' => 'user',
            'username' => 'user',
            'email' => 'user',
            'password' => $password,
        ]);

        $user->email_verified_at = now();
        $user->save();
    }
}
