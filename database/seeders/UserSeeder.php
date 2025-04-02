<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'email' => 'admin@example.com',
                'phone' => '9276192326',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Gwyneth Christine',
                'last_name' => 'Lee',
                'email' => 'gclee@mapuamcm.edu',
                'phone' => '9562347744',
                'role' => 'student',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Lexandrea',
                'last_name' => 'Quinones',
                'email' => 'lquinones@mapuamcm.edu',
                'phone' => '9914585640',
                'role' => 'student',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Yhors',
                'last_name' => 'Bonguyan',
                'email' => 'ybonguyan@mapuamcm.edu',
                'phone' => '9155148894',
                'role' => 'student',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Kirkenth Dominic Ruan',
                'last_name' => 'Juson',
                'email' => 'kdrjuson@mapuamcm.edu',
                'phone' => '9514092445',
                'role' => 'student',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Roosevelt Basil',
                'last_name' => 'Pancito',
                'email' => 'rbpancito@mapuamcm.edu',
                'phone' => '9457551723',
                'role' => 'student',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Samantha',
                'last_name' => 'Celestra',
                'email' => 'scelestra@mapuamcm.edu',
                'phone' => '9949526807',
                'role' => 'student',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'johndoe@security.com',
                'phone' => '9112345678',
                'role' => 'guard',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
