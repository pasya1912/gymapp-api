<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

//use faker


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faker = \Faker\Factory::create();

        $phoneNumber = '08' . rand(100000000, 999999999);

        $name = $faker->name;

        $address = $faker->address;
        $password = "admin";

        $user = User::create([
            'nama' => $name,
            'nomor' => $phoneNumber,
            'alamat' => $address,
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);
        print_r("nomor : " . $phoneNumber . "\n");
        print_r("password : " . $password . "\n");
   
    }
}
