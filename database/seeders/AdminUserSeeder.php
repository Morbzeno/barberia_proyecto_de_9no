<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Crea una cuenta de administrador lista para entrar al panel /admin.
     *
     * Credenciales de prueba:
     *   correo:    admin@machinbarber.test
     *   password:  Admin1234
     */
    public function run(): void
    {
        if (User::where('email', 'admin@machinbarber.test')->exists()) {
            return;
        }

        $user = User::create([
            'email' => 'admin@machinbarber.test',
            'password' => bcrypt('Admin1234'),
            'email_verified_at' => now(),
        ]);

        $person = Person::create([
            'name' => 'Administrador',
            'last_name' => 'Machin Barber',
            'phone_number' => '5555555555',
        ]);

        Employee::create([
            'userID' => $user->userID,
            'personID' => $person->personID,
            'payment' => 0,
            'schedule' => [
                ['day' => 'lunes', 'start' => '09:00', 'end' => '18:00'],
                ['day' => 'martes', 'start' => '09:00', 'end' => '18:00'],
                ['day' => 'miercoles', 'start' => '09:00', 'end' => '18:00'],
                ['day' => 'jueves', 'start' => '09:00', 'end' => '18:00'],
                ['day' => 'viernes', 'start' => '09:00', 'end' => '18:00'],
            ],
            'rfc' => 'ADMIN000000',
            'admin_type' => 'admin',
        ]);
    }
}
