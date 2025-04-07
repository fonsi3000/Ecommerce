<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\SubCategory;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Desactivar restricciones
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Limpiar tablas
        SubCategory::truncate();
        Category::truncate();

        // Reactivar restricciones
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Categorías principales
        $categorias = [
            'Maquillaje' => ['Ojos', 'Labios', 'Rostro'],
            'Cuidado Facial' => [],
            'Accesorios' => [],
            'Capilar' => [],
        ];

        foreach ($categorias as $nombreCategoria => $subcategorias) {
            $categoria = Category::create([
                'name' => $nombreCategoria,
                'slug' => Str::slug($nombreCategoria),
                'description' => 'Descripción de ' . $nombreCategoria,
            ]);

            foreach ($subcategorias as $sub) {
                SubCategory::create([
                    'category_id' => $categoria->id,
                    'name' => $sub,
                    'slug' => Str::slug($sub),
                    'description' => 'Subcategoría de ' . $nombreCategoria,
                ]);
            }
        }
    }
}
