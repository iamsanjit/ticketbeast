<?php

use Illuminate\Database\Seeder;
use App\Concert;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(Concert::class)->states('published')->create();
    }
}
