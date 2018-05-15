<?php

use Illuminate\Database\Seeder;
use App\Jobs;
// use Illuminate\Support\Facades\DB;

class JobsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Jobs::truncate();

        $faker = \Faker\Factory::create();

        for($i = 0; $i < 10; $i++) {
            Jobs::create([
                'submitterID' => $faker->randomNumber,
                'processorID' => NULL,
                'status' => 'SUBMITTED',
                'command' => $faker->sentence,
                'submittedOn' => $faker->dateTime,
                'priority' => $faker->randomNumber
            ]);
        }
    }
}
