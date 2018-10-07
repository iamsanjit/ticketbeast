<?php

use Illuminate\Database\Seeder;
use App\Concert;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(Concert::class)->states('published')->create()->addTickets(10);
        factory(User::class)->create([
            'email' => 'iamsanjit@hotmail.com',
            'password' => bcrypt('secret')
        ]);
    }
}
