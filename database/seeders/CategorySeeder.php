<?php

namespace Database\Seeders;

use App\Models\Category;
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
        $cat_arr = [
            ['name' => 'Books', 'slug' => 'books', 'status' => 1, 'main' => 1],
            ['name' => 'Lockets', 'slug' => 'lockets', 'status' => 1, 'main' => 1],
            ['name' => 'Photo Frame', 'slug' => 'photo-frame', 'status' => 1, 'main' => 1],
            ['name' => 'Showpiece', 'slug' => 'showpiece', 'status' => 1, 'main' => 1],
        ];

        // truncate the table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($cat_arr as $cat) {
            Category::create($cat);
        }
    }
}
