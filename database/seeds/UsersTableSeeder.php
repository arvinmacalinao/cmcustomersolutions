<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fake = Faker::create();

        DB::table('users')->insert([
            'name' => 'System',
            'email' => 'system@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 1,
            'company_id' => 1,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'admin@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 2,
            'company_id' => 1,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'HQ Admin',
            'email' => 'hqadmin@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 3,
            'company_id' => 1,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'Branch Admin',
            'email' => 'branchadmin@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 4,
            'company_id' => 3,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'Physical Encoder',
            'email' => 'pe@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 10,
            'company_id' => 1,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'HQ Technician',
            'email' => 'hqtech@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 7,
            'company_id' => 1,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'Branch Technician For MSN',
            'email' => 'branchtech@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 8,
            'company_id' => 3,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'HQ IOC',
            'email' => 'hqioc@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 5,
            'company_id' => 1,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'Branch IOC For MSN',
            'email' => 'branchioc@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 5,
            'company_id' => 3,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'Inventory Planning Control',
            'email' => 'inventory@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 12,
            'company_id' => 1,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'HQ Quality Controller',
            'email' => 'qc@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 9,
            'company_id' => 1,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'Branch Quality Controller',
            'email' => 'branchqc@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 9,
            'company_id' => 3,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'HQ Warehouse',
            'email' => 'warehouse@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 14,
            'company_id' => 1,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'Branch Warehouse',
            'email' => 'branchwarehouse@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 13,
            'company_id' => 3,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'RDU',
            'email' => 'rdu@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 16,
            'company_id' => 1,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        DB::table('users')->insert([
            'name' => 'Special Case User',
            'email' => 'sc@cherrymobile.com',
            'password' => bcrypt('CherryM'),
            'role_id' => 11,
            'company_id' => 1,
            'created_by' => 1,
            'created_at' => date("Y-m-d H:i:s"),
            'flag' => true,
        ]);

        /*foreach(range(1, 5) as $index)
        {
            DB::table('users')->insert([
                'name' => $fake->name,
                'email' => $fake->safeEmail,
                'password' => bcrypt('CherryM'),
                'role_id' => $fake->numberBetween(1,15),
                'company_id' => $fake->numberBetween(1,4),
                'created_by' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'flag' => true,
            ]);
        }*/
    }
}