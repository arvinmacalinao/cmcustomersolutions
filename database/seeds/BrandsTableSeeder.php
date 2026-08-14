<?php

use Illuminate\Database\Seeder;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = ['Cherry Mobile', 'Cubix'];
    	
    	foreach ($brands as $key => $brand) {
    		DB::table('brands')->insert([
                'name' => $brand,
                'flag' => true,
		        'created_by' => 1,
		        'created_at' => date("Y-m-d H:i:s"),
	        ]);
    	}
    }
}
