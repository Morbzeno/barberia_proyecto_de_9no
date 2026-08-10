<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::first();

        if (!$category) {
            $this->command->error(
                'No existen categorías. Crea al menos una categoría antes de ejecutar el seeder.'
            );

            return;
        }

        $products = [
            [
                'name' => 'Pomada Matte Premium',
                'sell_price' => 180,
                'wholesale_price' => 150,
                'buy_price' => 95,
                'bar_code' => 900000001,
                'stock' => 25,
                'description' => 'Pomada de fijación media con acabado mate.',
                'image' => 'products/714GE6IDrOL.jpg',
            ],
            [
                'name' => 'Aceite para Barba',
                'sell_price' => 220,
                'wholesale_price' => 180,
                'buy_price' => 120,
                'bar_code' => 900000002,
                'stock' => 18,
                'description' => 'Aceite hidratante para suavizar y cuidar la barba.',
                'image' => 'products/71Ki7HRfLxL._AC_UF1000,1000_QL80_.jpg',
            ],
            [
                'name' => 'Shampoo Profesional',
                'sell_price' => 190,
                'wholesale_price' => 155,
                'buy_price' => 100,
                'bar_code' => 900000003,
                'stock' => 30,
                'description' => 'Shampoo de limpieza profunda para uso profesional.',
                'image' => 'products/vitamino-color-spectrum-purple-shampoo-slider1.jpg',
            ],
            [
                'name' => 'Cera para Cabello',
                'sell_price' => 160,
                'wholesale_price' => 130,
                'buy_price' => 80,
                'bar_code' => 900000004,
                'stock' => 22,
                'description' => 'Cera de fijación fuerte para peinados definidos.',
                'image' => 'products/714GE6IDrOL.jpg',
            ],
            [
                'name' => 'Bálsamo para Barba',
                'sell_price' => 210,
                'wholesale_price' => 170,
                'buy_price' => 110,
                'bar_code' => 900000005,
                'stock' => 15,
                'description' => 'Bálsamo para hidratar y dar forma a la barba.',
            'image' => 'products/8006569147820.jpg',
            ],
            [
                'name' => 'Spray Fijador',
                'sell_price' => 175,
                'wholesale_price' => 140,
                'buy_price' => 90,
                'bar_code' => 900000006,
                'stock' => 20,
                'description' => 'Spray de fijación para mantener el peinado durante el día.',
                'image' => 'products/images (1).jpg',
            ],
        ];

        foreach ($products as $data) {

            DB::transaction(function () use ($data, $category) {

                // Crear o actualizar para que el seeder pueda ejecutarse
                // más de una vez sin generar productos duplicados
                $product = Product::updateOrCreate(
                    [
                        'bar_code' => $data['bar_code'],
                    ],
                    [
                        'categoryID' => $category->categoryID,
                        'name' => $data['name'],
                        'sell_price' => $data['sell_price'],
                        'wholesale_price' => $data['wholesale_price'],
                        'buy_price' => $data['buy_price'],
                        'stock' => $data['stock'],
                        'description' => $data['description'],
                        'state' => 'ACTIVO',
                    ]
                );

                // Buscar o crear el registro de la imagen
                $image = Image::firstOrCreate(
                    [
                        'image' => $data['image'],
                    ]
                );

                // Relacionar la imagen con el producto
                $product->images()->syncWithoutDetaching([
                    $image->imageID
                ]);
            });
        }

        $this->command->info(
            'Productos e imágenes creados correctamente.'
        );
    }
}