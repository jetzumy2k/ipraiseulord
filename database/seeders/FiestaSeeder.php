<?php

namespace Database\Seeders;

use App\Services\FiestaSeedService;
use Illuminate\Database\Seeder;

class FiestaSeeder extends Seeder
{
    public function run(): void
    {
        app(FiestaSeedService::class)->seed();
    }
}
