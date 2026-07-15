<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use App\Models\Chair;
use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class GeneralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faker = \Faker\Factory::create();

        $diasSemana = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        DB::table('users')->insert([
            'email' => Str::random(10).'@example.com',
            'password' => bcrypt('password'),
        ]);

        DB::table('persons')->insert([
            'name' => Str::random(10),
            'last_name' => Str::random(10),
            'phone_number' => 333333333
        ]);

        DB::table('users')->insert([
            'email' => Str::random(10).'@example.com',
            'password' => bcrypt('password'),
        ]);

        DB::table('persons')->insert([
            'name' => Str::random(10),
            'last_name' => Str::random(10),
            'phone_number' => 333333333
        ]);

        DB::table('clients')->insert([
            'userID' => 1,
            'personID' => 1
        ]);

        for ($i = 0; $i < 5; $i++) {
            
             $randomSchedule = [
                 'days' => $faker->randomElements($diasSemana, rand(2, 4)),
                 'hours' => [
                     'start' => '09:00',
                    'end' => '18:00'
                 ]
            ];
            
            DB::table('employees')->insert([
                'userID' => 2,
                'personID' => 2,
                'payment' => $faker->randomFloat(2, 10, 10000),
                    
                // Convertimos el array a JSON para guardarlo en la BD
                    'schedule' => json_encode($randomSchedule),

                'rfc' => Str::random(13),
                    
                // Selecciona aleatoriamente entre 'barber' o 'admin'
                'admin_type' => $faker->randomElement(['barber', 'admin']),
                    
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('services')->insert([
            'name' => Str::random(10),
            'description' => Str::random(255),
            'price' => $faker->randomFloat(2, 10, 1000),
            'aproxDuration' => $faker->randomNumber(2),
        ]);
        DB::table('services')->insert([
            'name' => Str::random(10),
            'description' => Str::random(255),
            'price' => $faker->randomFloat(2, 10, 1000),
            'aproxDuration' => $faker->randomNumber(2),
        ]);

        $servicesIds = Service::pluck('serviceID')->toArray();
        Chair::factory()->count(5)->create()->each(function ($chair) use ($servicesIds) {
            
            if (!empty($servicesIds)) {
                $randomServices = array_rand(array_flip($servicesIds), rand(1, 3));
                $servicesToAttach = is_array($randomServices) ? $randomServices : [$randomServices];
                $chair->services()->attach($servicesToAttach);
            }
        });

    }
}
