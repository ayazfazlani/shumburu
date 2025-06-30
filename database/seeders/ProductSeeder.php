<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
  public function run(): void
  {
    $products = [
      // 20mm pipes
      [
        'name' => 'HDPE Pipe 20mm PN6',
        'code' => 'OS1',
        'size' => '20mm',
        'pn' => 'PN6',
        'meter_length' => 100.00,
      ],
      [
        'name' => 'HDPE Pipe 20mm PN10',
        'code' => 'OS2',
        'size' => '20mm',
        'pn' => 'PN10',
        'meter_length' => 100.00,
      ],
      // 32mm pipes
      [
        'name' => 'HDPE Pipe 32mm PN6',
        'code' => 'OS3',
        'size' => '32mm',
        'pn' => 'PN6',
        'meter_length' => 100.00,
      ],
      [
        'name' => 'HDPE Pipe 32mm PN10',
        'code' => 'OS4',
        'size' => '32mm',
        'pn' => 'PN10',
        'meter_length' => 100.00,
      ],
      // 110mm pipes
      [
        'name' => 'HDPE Pipe 110mm PN6',
        'code' => 'OS5',
        'size' => '110mm',
        'pn' => 'PN6',
        'meter_length' => 100.00,
      ],
      [
        'name' => 'HDPE Pipe 110mm PN10',
        'code' => 'OS6',
        'size' => '110mm',
        'pn' => 'PN10',
        'meter_length' => 100.00,
      ],
    ];

    foreach ($products as $product) {
      Product::create($product);
    }
  }
}
