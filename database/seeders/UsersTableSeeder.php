<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users=[
            [
                "name"=> "Admin",
                "email"=> "Admin@gmail.com",
                "password"=> bcrypt("password"),
                "remember_token" => null
            ],
            [
                "name"=> "Agent",
                "email"=> "agent@gmail.com",
                "password"=> bcrypt("password"),
                "remember_token" => null
            ],
        ];
        User::insert($users);
    }
}
