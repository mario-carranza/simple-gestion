<?php

use Illuminate\Database\Seeder;

class CompanyUsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('company_users')->delete();

        \DB::table('company_users')->insert(array (
            0 =>
            array (
                'company_id' => 1,
                'created_at' => NULL,
                'id' => 1,
                'role_id' => 1,
                'updated_at' => NULL,
                'user_id' => 1,
            ),
        ));


    }
}
