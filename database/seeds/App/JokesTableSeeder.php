<?php

use Illuminate\Database\Seeder;
use App\Joke;
// composer require laracasts/testdummy

class JokesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();

        foreach(range(1,30) as $index)
        {
            Joke::create([
                'joke' => $faker->paragraph($nbSentences = 3),
                'user_id' =>$faker->numberBetween($min = 1, $max = 5)
            ]);
        }
    }

}
