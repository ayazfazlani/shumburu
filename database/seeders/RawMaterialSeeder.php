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
      ],
      [
        'name' => 'Carbon Black',
        'code' => 'CARBON-BLACK',
        'description' => 'Carbon Black for coloring',
        'unit' => 'kg',
      ],
      [
        'name' => 'UV Stabilizer',
        'code' => 'UV-STAB',
        'description' => 'UV Stabilizer for protection',
        'unit' => 'kg',
      ],
      [
        'name' => 'Antioxidant',
        'code' => 'ANTIOX',
        'description' => 'Antioxidant additive',
        'unit' => 'kg',
      ],
      [
        'name' => 'Processing Aid',
        'code' => 'PROC-AID',
        'description' => 'Processing aid for extrusion',
        'unit' => 'kg',
      ],
      [
        'name' => 'Lubricant',
        'code' => 'LUBE',
        'description' => 'Lubricant for smooth processing',
        'unit' => 'kg',
      ],
      [
        'name' => 'Color Masterbatch',
        'code' => 'COLOR-MB',
        'description' => 'Color masterbatch for pigmentation',
        'unit' => 'kg',
      ],
    ];

    foreach ($rawMaterials as $material) {
      RawMaterial::create($material);
    }
  }
}
