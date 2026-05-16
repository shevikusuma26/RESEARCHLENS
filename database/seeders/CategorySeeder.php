<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Artificial Intelligence & Machine Learning',
            'Web Development & Cloud Computing',
            'Mobile Development & IoT',
            'Data Science & Big Data',
            'Cybersecurity & Network',
            'Game Development & Animation',
            'Healthcare Technology',
            'E-Commerce & Finance Technology',
            'Environmental & Sustainability',
            'Education Technology',
            'Computer Vision',
            'Natural Language Processing',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'category_name' => $category,
            ], [
                'description' => 'Research in ' . $category,
            ]);
        }
    }
}
