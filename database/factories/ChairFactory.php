<?php

namespace Database\Factories;

use App\Models\Chair;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chair>
 */
class ChairFactory extends Factory
{
    
    protected $model = Chair::class;

        public function definition(): array
        {
            return [
                // Genera un nombre ficticio para la silla, ej: "Silla 1" o "Silla VIP"
                'chairName' => 'Silla ' . $this->faker->unique()->numberBetween(1, 20),
            ];
        }
        
}
