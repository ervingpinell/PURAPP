<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('roles')->insertOrIgnore([
            ['role_id'=>1,'role_name'=>'Admin','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['role_id'=>2,'role_name'=>'Supervisor','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['role_id'=>3,'role_name'=>'Customer','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
        ]);

        DB::table('users')->insertOrIgnore([
            [
                'full_name'=>'Erving Pinell',
                'email'=>'ervingpinell@gmail.com',
                'password'=>Hash::make('-erving1234'),
                'role_id'=>1, 'phone'=>'24791471',
                'status'=>true, 'is_active'=>true, 'created_at'=>$now, 'updated_at'=>$now
            ],
            [
                'full_name'=>'Axel Paniagua',
                'email'=>'axelpaniaguab54@gmail.com',
                'password'=>Hash::make('-12345678.'),
                'role_id'=>1, 'phone'=>'72612748',
                'status'=>true, 'is_active'=>true, 'created_at'=>$now, 'updated_at'=>$now
            ],
                        [
                'full_name'=>'Admin',
                'email'=>'info@greenvacationscr.com',
                'password'=>Hash::make('Green1974*'),
                'role_id'=>1, 'phone'=>'24791471',
                'status'=>true, 'is_active'=>true, 'created_at'=>$now, 'updated_at'=>$now
            ]
        ]);
    }
}
