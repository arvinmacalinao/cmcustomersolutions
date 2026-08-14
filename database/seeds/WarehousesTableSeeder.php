<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class WarehousesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$fake = Faker::create();
    	$states = DB::table('states')->where('flag', true)->lists('id');
        $warehouse_names = ['HQ Warehouse', 'MSN Lifestyle Concepts Warehouse'];
        $company_id = [1, 3];

        foreach ($warehouse_names as $key => $name) {
            DB::table('warehouses')->insert([
                'name' => $name,
                'company_id' => $company_id[$key],
                'address' => $fake->streetAddress,
                'postcode' => $fake->postcode,
                'state_id' => $fake->randomElement($states),
                'status' => true,
                'flag' => true,
                'created_by' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }
}
