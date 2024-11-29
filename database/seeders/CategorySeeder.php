<?php

namespace Database\Seeders;

use App\Models\Tenants\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (in_array(config('database.default'), ['nativephp', 'sqlite'])) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        Category::truncate();
        Category::create([
            'name' => 'UMUM',
        ]);
    }
}
