<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DeviceModelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fake = Faker::create();
        $device_types = ['Feature Phone', 'Smart Phone', 'Tablet'];
    	//$models = ['1202I', '1800i', 'A5', 'A5I', 'A6'];

        foreach ($device_types as $key => $type) {
            DB::table('device_types')->insert([
                'name' => $type,
                'flag' => true,
                'created_by' => 1,
                'created_at' => date("Y-m-d H:i:s"),
            ]);
        }
    	
    	/*foreach ($models as $key => $model) {
    		DB::table('device_models')->insert([
	            'name' => $model,
                'code' => $model,
                'brand_id' => 1,
                'device_type_id' => 1,
		        'warranty' => $fake->numberBetween(1, 30),
		        'price' => $fake->randomFloat(2, 10, 90000),
                'labor_cost_1' => $fake->randomFloat(2, 0, 1000),
                'labor_cost_2' => $fake->randomFloat(2, 0, 1000),
                'labor_cost_3' => $fake->randomFloat(2, 0, 1000),
		        'created_by' => 1,
		        'created_at' => date("Y-m-d H:i:s"),
	        ]);
    	}*/
    }
}