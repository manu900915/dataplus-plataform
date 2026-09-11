<?php

namespace Database\Seeders;

use App\Models\Almacen;
use App\Models\CategoriaItem;
use Illuminate\Database\Seeder;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        CategoriaItem::insert(collect([
            ['nombre' => 'Insecticidas y plaguicidas', 'tipo' => 'material'],
            ['nombre' => 'Cebos y trampas', 'tipo' => 'material'],
            ['nombre' => 'Envases y consumibles', 'tipo' => 'material'],
            ['nombre' => 'Equipos de aspersión', 'tipo' => 'equipamiento'],
            ['nombre' => 'Herramientas', 'tipo' => 'equipamiento'],
            ['nombre' => 'EPI (protección personal)', 'tipo' => 'ambos'],
        ])->map(fn ($c) => $c + ['activo' => true, 'created_at' => $now, 'updated_at' => $now])->all());

        Almacen::insert([
            ['nombre' => 'Almacén Central', 'provincia' => null, 'municipio' => null, 'responsable_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Almacén Secundario', 'provincia' => null, 'municipio' => null, 'responsable_id' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}