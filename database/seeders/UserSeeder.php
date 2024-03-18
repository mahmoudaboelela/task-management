<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = array(
            [
                'name'=>'Ahmed Khaled',
                'email'=>'ahmed@app.com',
                'password' => Hash::make("12345678")
            ],
            [
                'name'=>'Mohamed Ibrahim',
                'email'=>'mohamed@app.com',
                'password' => Hash::make("12345678")
            ],
            [
                'name'=>'Nada Ahmed',
                'email'=>'nada@app.com',
                'password' => Hash::make("12345678")
            ],
        );

        foreach ($users as $user){
            $userModel = User::create($user);
            $userModel->assignRole("User");
        }
    }
}
