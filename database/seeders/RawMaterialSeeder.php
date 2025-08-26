<?php

namespace Database\Seeders;

use App\Models\RawMaterial;
use Illuminate\Database\Seeder;

class RawMaterialSeeder extends Seeder
{
  public function run(): void
  {
    $rawMaterials = [
      [
        'name' => 'HDPE Resin',
        'code' => 'HDPE-RESIN',
        'description' => 'High Density Polyethylene Resin',
        'unit' => 'kg',
        'quantity' => 1000.000,
      ],
      [
        'name' => 'Carbon Black',
        'code' => 'CARBON-BLACK',
        'description' => 'Carbon Black for coloring',
        'unit' => 'kg',
        'quantity' => 500.000,
      ],
      [
        'name' => 'UV Stabilizer',
        'code' => 'UV-STAB',
        'description' => 'UV Stabilizer for protection',
        'unit' => 'kg',
        'quantity' => 200.000,
      ],
      [
        'name' => 'Antioxidant',
        'code' => 'ANTIOX',
        'description' => 'Antioxidant additive',
        'unit' => 'kg',
        'quantity' => 150.000,
      ],
      [
        'name' => 'Processing Aid',
        'code' => 'PROC-AID',
        'description' => 'Processing aid for extrusion',
        'unit' => 'kg',
        'quantity' => 100.000,
      ],
      [
        'name' => 'Lubricant',
        'code' => 'LUBE',
        'description' => 'Lubricant for smooth processing',
        'unit' => 'kg',
        'quantity' => 50.000,
      ],
      [
        'name' => 'Color Masterbatch',
        'code' => 'COLOR-MB',
        'description' => 'Color masterbatch for pigmentation',
        'unit' => 'kg',
        'quantity' => 300.000,
      ]
    ];

    foreach ($rawMaterials as $material) {
      RawMaterial::create($material);
    }
  }
}
