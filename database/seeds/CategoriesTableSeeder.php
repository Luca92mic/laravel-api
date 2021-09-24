<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = ['Html', 'Css', 'Js', 'Laravel']; 

        foreach($categories as $category){

            $newCategories = new Category();

            $newCategories->name = $category;

            $slug = Str::slug($category, '-');
            $newCategories->slug = $slug;

            $newCategories->save();
        }
    }
}
