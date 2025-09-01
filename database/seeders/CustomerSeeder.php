<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        
        $customers = [];
        
        for ($i = 0; $i < 50; $i++) {
            $companyName = $faker->company;
            $code = strtoupper(substr($companyName, 0, 3)) . $faker->numberBetween(100, 999);
            
            $customers[] = [
                'code' => $code,
                'name' => $companyName,
                'contact_person' => $faker->name,
                'phone' => $faker->phoneNumber,
                'email' => $faker->unique()->companyEmail,
                'address' => $faker->streetAddress . ', ' . $faker->city . ', ' . $faker->state . ' ' . $faker->postcode,
                'is_active' => $faker->boolean(90), // 90% chance of being active
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        DB::table('customers')->insert($customers);
    }
}