<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\Category;
use App\Models\FinalProject;
use App\Models\Keyword;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed categories first
        $this->call(CategorySeeder::class);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@researchlens.com'],
            [
                'name'              => 'Admin ResearchLens',
                'email_verified_at' => now(),
                'password'          => Hash::make('admin123456'),
                'role'              => 'admin',
                'bio'               => 'Administrator of ResearchLens Platform',
                'student_id'        => null,
                'phone'             => '081234567890',
            ]
        );

        // Create sample mahasiswa users
        $mahasiswaUsers = User::factory(10)->create(['role' => 'mahasiswa']);

        // Sample keywords pool
        $keywordPool = [
            'machine learning', 'deep learning', 'artificial intelligence', 'neural network',
            'natural language processing', 'computer vision', 'data mining', 'big data',
            'internet of things', 'blockchain', 'cloud computing', 'cybersecurity',
            'mobile application', 'web development', 'database', 'algorithm',
            'recommendation system', 'chatbot', 'image recognition', 'sentiment analysis',
        ];

        // Create sample final projects for each mahasiswa
        foreach ($mahasiswaUsers as $user) {
            $count = rand(1, 3);
            for ($i = 0; $i < $count; $i++) {
                $project = FinalProject::factory()->create(['user_id' => $user->id]);

                // Add keywords to project
                $projectKeywords = array_rand(array_flip($keywordPool), rand(3, 6));
                foreach ((array) $projectKeywords as $kw) {
                    Keyword::firstOrCreate([
                        'final_project_id' => $project->id,
                        'keyword'          => $kw,
                    ]);
                }

                // Create notification for user
                UserNotification::create([
                    'user_id' => $user->id,
                    'title'   => 'Proyek Berhasil Ditambahkan',
                    'message' => "Proyek \"{$project->title}\" telah berhasil ditambahkan ke sistem.",
                    'type'    => 'success',
                    'is_read' => false,
                ]);
            }
        }

        // Create API key for admin
        ApiKey::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'api_key'       => 'rl_' . Str::random(32),
                'name'          => 'Admin Default Key',
                'status'        => 'active',
                'request_count' => 0,
            ]
        );

        // Create some similarity analysis notifications
        $projects = FinalProject::all();
        if ($projects->count() > 1) {
            $firstProject = $projects->first();
            UserNotification::create([
                'user_id' => $firstProject->user_id,
                'title'   => 'Analisis Similarity Selesai',
                'message' => "Analisis similarity untuk proyek \"{$firstProject->title}\" telah selesai. Skor similarity: {$firstProject->similarity_score}%",
                'type'    => 'info',
                'is_read' => false,
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@researchlens.com / admin123456');
        $this->command->info('Mahasiswa: (10 users with password: password123)');
    }
}
