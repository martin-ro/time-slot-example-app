<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         // User::factory(10)->create();

         $user = User::factory()->create([
             'name' => 'Test User',
             'email' => 'test@example.com',
         ]);

//        Lesson::create([
//            'start' => Carbon::createFromFormat('d/m/Y H:i:s', '10/11/2022 14:00:00')->tz('UTC'),
//            'end' => Carbon::createFromFormat('d/m/Y H:i:s',  '10/11/2022 14:30:00')->tz('UTC'),
//            'user_id' => $user->id,
//        ]);

        Lesson::create([
            'start' => now()->startOfDay()->addDay()->addHours(8),
            'end' => now()->startOfDay()->addDay()->addHours(8)->addMinutes(60),
            'user_id' => $user->id,
        ]);

        Lesson::create([
            'start' => now()->startOfDay()->addDay()->addHours(9),
            'end' => now()->startOfDay()->addDay()->addHours(9)->addMinutes(60),
            'user_id' => $user->id,
        ]);

        Lesson::create([
            'start' => now()->startOfDay()->addDay()->addHours(10),
            'end' => now()->startOfDay()->addDay()->addHours(10)->addMinutes(60),
            'user_id' => $user->id,
        ]);
    }
}
