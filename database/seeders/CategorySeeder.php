<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Fiction',
            'Non-Fiction',
            'Science',
            'History',
            'Technology',
            'Biography',
            'Philosophy',
            'Children',
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['category_name' => $cat]);
        }
    }
}
