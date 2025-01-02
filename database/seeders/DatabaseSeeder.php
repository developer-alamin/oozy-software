<?php

namespace Database\Seeders;

use App\Http\Controllers\HelperController;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Factory;
use App\Models\Floor;
use App\Models\Line;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call AdminSeeder first (if needed)
        $this->call([AdminSeeder::class]);

    }
}
