<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinalProjectFactory extends Factory
{
    private array $titles = [
        'Sistem Deteksi Plagiarisme Berbasis Machine Learning pada Karya Tulis Ilmiah',
        'Implementasi Deep Learning untuk Klasifikasi Penyakit Tanaman Padi',
        'Pengembangan Aplikasi E-Learning Adaptif Menggunakan Algoritma Collaborative Filtering',
        'Analisis Sentimen Media Sosial Menggunakan BERT untuk Deteksi Hoaks',
        'Sistem Rekomendasi Wisata Berbasis Knowledge Graph dan NLP',
        'Implementasi IoT untuk Monitoring Kualitas Udara Berbasis Arduino',
        'Pengembangan Framework Keamanan API Berbasis Zero-Trust Architecture',
        'Sistem Prediksi Prestasi Mahasiswa Menggunakan Algoritma Random Forest',
        'Analisis Pola Konsumsi Energi Berbasis Big Data pada Smart Building',
        'Pengembangan Chatbot Akademik Berbasis Transformer untuk Sistem Informasi Kampus',
        'Implementasi Blockchain untuk Keamanan Data Nilai Akademik Mahasiswa',
        'Sistem Deteksi Anomali Jaringan Menggunakan LSTM Neural Network',
        'Pengembangan Aplikasi Mobile untuk Pemantauan Kesehatan Berbasis Wearable IoT',
        'Optimasi Rute Pengiriman Logistik Menggunakan Algoritma Genetika',
        'Sistem Manajemen Perpustakaan Digital Berbasis Semantic Web dan Linked Data',
    ];

    private array $abstracts = [
        'Penelitian ini mengembangkan sistem cerdas berbasis kecerdasan buatan untuk mendeteksi dan menganalisis kesamaan konten pada dokumen akademik. Sistem menggunakan algoritma machine learning yang dikombinasikan dengan pemrosesan bahasa alami untuk memberikan hasil analisis yang akurat dan efisien.',
        'Sistem yang dikembangkan dalam penelitian ini memanfaatkan teknologi Internet of Things dan kecerdasan buatan untuk monitoring lingkungan secara real-time. Data sensor dikumpulkan dan diproses menggunakan algoritma machine learning untuk memberikan prediksi dan rekomendasi yang akurat.',
        'Penelitian ini berfokus pada pengembangan platform digital yang mengintegrasikan berbagai teknologi modern untuk meningkatkan pengalaman pengguna. Sistem menggunakan arsitektur microservices dengan RESTful API yang aman dan skalabel.',
        'Studi ini menganalisis efektivitas algoritma deep learning dalam menyelesaikan permasalahan klasifikasi yang kompleks. Dataset yang digunakan mencakup ribuan sampel dengan berbagai karakteristik untuk memastikan model yang dikembangkan memiliki generalisasi yang baik.',
        'Penelitian ini mengimplementasikan teknik analisis data besar untuk mengekstrak pola dan insight yang bermakna dari dataset skala besar. Metodologi yang digunakan mencakup preprocessing data, feature engineering, dan model ensemble untuk menghasilkan prediksi yang akurat.',
    ];

    public function definition()
    {
        $categoryId = Category::inRandomOrder()->first()?->id ?? 1;
        $userId = User::where('role', 'mahasiswa')->inRandomOrder()->first()?->id ?? 1;

        return [
            'user_id'          => $userId,
            'category_id'      => $categoryId,
            'title'            => $this->faker->randomElement($this->titles),
            'abstract'         => $this->faker->randomElement($this->abstracts) . ' ' . $this->faker->paragraph(3),
            'research_method'  => 'Penelitian ini menggunakan metode ' . $this->faker->randomElement(['kuantitatif', 'kualitatif', 'mixed method']) . ' dengan pendekatan eksperimental. Data dianalisis menggunakan ' . $this->faker->randomElement(['SPSS', 'Python', 'R']) . '.',
            'novelty_score'    => $this->faker->randomFloat(2, 30, 95),
            'similarity_score' => $this->faker->randomFloat(2, 5, 45),
            'status'           => $this->faker->randomElement(['draft', 'submitted', 'analyzed']),
        ];
    }
}
