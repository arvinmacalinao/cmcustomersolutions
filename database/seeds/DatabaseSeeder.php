<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Truncate table
        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
            //User::truncate(); //Uses the eloquent model to truncate.
            DB::table('customers')->truncate();
            DB::table('e_warranties')->truncate();
            DB::table('device_types')->truncate();
            DB::table('device_models')->truncate();
            DB::table('brands')->truncate();
            DB::table('users')->truncate();
            DB::table('warehouses')->truncate();
            //DB::table('companies')->truncate();
            //DB::table('states')->truncate(); // Data will be imported manually.
            DB::table('countries')->truncate();

            // Start seeding table
            //$this->call(EWarrantiesTableSeeder::class);
            $this->call(CountriesTableSeeder::class);
            $this->call(WarehousesTableSeeder::class);
            //$this->call(StatesTableSeeder::class);
            //$this->call(CompaniesTableSeeder::class);
            $this->call(UsersTableSeeder::class);
            $this->call(BrandsTableSeeder::class);
            $this->call(DeviceModelsTableSeeder::class);
            $this->call(CustomersTableSeeder::class);
            //factory(App\User::class, 5)->create();
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");

        Model::reguard();
    }
}
