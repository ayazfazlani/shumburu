<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
  public function run(): void
  {
    $departments = [
      [
        'name' => 'Warehouse',
        'code' => 'WH',
        'description' => 'Warehouse and storage management',
      ],
      [
        'name' => 'Operations',
        'code' => 'OPS',
        'description' => 'Production and operations team',
      ],
      [
        'name' => 'Finance',
        'code' => 'FIN',
        'description' => 'Finance and accounting department',
      ],
      [
        'name' => 'Sales',
        'code' => 'SALES',
        'description' => 'Sales and customer relations',
      ],
      [
        'name' => 'Administration',
        'code' => 'ADMIN',
        'description' => 'System administration and management',
      ],
    ];

    foreach ($departments as $department) {
      Department::create($department);
    }
  }
}
