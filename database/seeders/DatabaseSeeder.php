<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\Database\Eloquent\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         User::factory(10)->create();
    }
}
