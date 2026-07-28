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


        // Insertar servicios
        DB::table('services')->insert([
            'name' => "Corte clásico",
            'description' => "Tijera y máquina, acabado a navaja",
            'price' => 280.0,
            'aproxDuration' => 20,
        ]);
        DB::table('services')->insert([
            'name' => "Fade moderno",
            'description' => "Degradado de precisión y diseño",
            'price' => 350.0,
            'aproxDuration' => 30,
        ]);
        DB::table('services')->insert([
            'name' => "Corte niño",
            'description' => "Hasta 12 años",
            'price' => 220.0,
            'aproxDuration' => 10,
        ]);
        DB::table('services')->insert([
            'name' => "Spa Premium",
            'description' => "Corte + limpieza facial + exfoliación",
            'price' => 450.0,
            'aproxDuration' => 45,
        ]);
        DB::table('services')->insert([
            'name' => "Limpieza facial",
            'description' => "Vapor, exfoliación y mascarilla",
            'price' => 300.0,
            'aproxDuration' => 30,
        ]);
        DB::table('services')->insert([
            'name' => "Barba & Estilo",
            'description' => "Perfilado, toalla caliente y aceites premium",
            'price' => 260.0,
            'aproxDuration' => 25,
        ]);
        DB::table('services')->insert([
            'name' => "Afeitado clásico",
            'description' => "Toalla caliente y navaja",
            'price' => 240.0,
            'aproxDuration' => 20,
        ]);
        DB::table('services')->insert([
            'name' => "Perfilado de cejas",
            'description' => "Perfilado de cejas",
            'price' => 120.0,
            'aproxDuration' => 20,
        ]);
        DB::table('services')->insert([
            'name' => "Ritual Machin",
            'description' => "Corte + barba + spa premium",
            'price' => 680.0,
            'aproxDuration' => 50,
        ]);

        // Lista de sillas con sus IDs de servicios a asociar
        $chairsData = [
            ['chairName' => 'silla 1', 'services' => [1, 2, 3]],
            ['chairName' => 'silla 2', 'services' => [1, 2, 3]],
            ['chairName' => 'silla 3', 'services' => [1, 2, 3, 6, 7, 8]],
            ['chairName' => 'silla 4', 'services' => [1, 2, 3, 6, 7, 8]],
            ['chairName' => 'silla 5', 'services' => [1, 2, 3, 6, 7, 8]],
            ['chairName' => 'silla 6', 'services' => [1, 2, 3, 6, 7, 8]],
            ['chairName' => 'silla 7', 'services' => [1, 2, 3, 4, 5, 6, 7, 8, 9]],
            ['chairName' => 'silla 8', 'services' => [1, 2, 3, 4, 5, 6, 7, 8, 9]],
        ];

        foreach ($chairsData as $data) {
            // 1. Crear el registro en la tabla 'chairs'
            $chair = Chair::create([
                'chairName' => $data['chairName'],
            ]);

            // 2. Adjuntar los servicios en la tabla pivote
            $chair->services()->attach($data['services']);
        }
    }
}
