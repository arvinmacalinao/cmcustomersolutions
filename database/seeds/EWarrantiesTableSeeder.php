<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class EWarrantiesTableSeeder extends Seeder
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

        foreach(range(1, 50) as $index)
        {
            DB::table('e_warranties')->insert([
                //'imei' => $fake->numberBetween(10000000000000,9999999999999999999),
                'imei' => $fake->creditCardNumber,
                'frontliner_code' => $fake->word,
                'model' => $fake->word,
                'customer_name' => $fake->name,
                'age' => $fake->numberBetween(1,130),
                'gender' => $fake->randomElement($gender),
                'email' => $fake->safeEmail,
                'phone_number' => $fake->phoneNumber,
                'location' => $fake->address,
                'city' => $fake->city,
                'status' => $fake->numberBetween(1, 4),
                'created_by' => 1,
                'created_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }
}
