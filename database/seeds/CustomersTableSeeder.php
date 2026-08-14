<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$fake = Faker::create();
    	$gender = ['male', 'female'];
    	$states = DB::table('states')->where('flag', true)->lists('id');

        foreach(range(1, 10) as $index)
        {
            DB::table('customers')->insert([
                'name' => $fake->name,
                'email' => $fake->safeEmail,
                'gender' => $fake->randomElement($gender),
                'dob' => $fake->date($format = 'Y-m-d', $max = '-10 years'),
                'id_type' => $fake->numberBetween(1,5),
                'id_number' => $fake->swiftBicNumber,
                'mobile_number' => $fake->e164PhoneNumber,
                'address' => $fake->streetAddress,
                'postcode' => $fake->postcode,
                'state_id' => $fake->randomElement($states),
                'country_id' => 1,
                'flag' => true,
                'created_by' => 1,
                'created_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }
}