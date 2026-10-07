<?php

namespace Database\Seeders;

use App\Modules\Actividades\Database\Seeders\ActividadesRutasSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->callSilent(ActividadesRutasSeeder::class);
    }
}
