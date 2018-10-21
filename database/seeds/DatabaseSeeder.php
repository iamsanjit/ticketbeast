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
        $user = factory(User::class)->create([
            'email' => 'iamsanjit@hotmail.com',
            'password' => bcrypt('secret')
        ]);
        \ConcertFactory::createPublished(['ticket_quantity' => 10, 'user_id' => $user->id]);
        \ConcertFactory::createPublished(['ticket_quantity' => 10, 'user_id' => $user->id]);
        \ConcertFactory::createUnpublished(['ticket_quantity' => 10, 'user_id' => $user->id]);

        \ConcertFactory::createPublished(['ticket_quantity' => 10]);
    }
}
