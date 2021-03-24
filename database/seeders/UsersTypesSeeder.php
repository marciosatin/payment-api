<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users_types')->insert([
            ['id' => 1, 'type_name' => UserType::TYPE_COMUM],
            ['id' => 2, 'type_name' => UserType::TYPE_LOJISTA],
        ]);
    }
}
