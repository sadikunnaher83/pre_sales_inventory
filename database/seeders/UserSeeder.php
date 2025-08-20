<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'mobile' => '01768470783',
            'email' => 'admin@gmail.com',
            // 'password' => Hash::make('1111'),
            'password' => '1111',

        ]);
    }
}
