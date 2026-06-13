<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@library.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Perpustakaan No. 1, Jakarta',
            'email_verified_at' => now(),
        ]);

        // Create Member
        User::create([
            'name' => 'Anggota Contoh',
            'email' => 'member@library.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'phone' => '089876543210',
            'address' => 'Jl. Pembaca No. 2, Bandung',
            'email_verified_at' => now(),
        ]);

        // Create Categories
        $categories = [
            ['name' => 'Fiksi', 'description' => 'Buku-buku fiksi dan novel'],
            ['name' => 'Non-Fiksi', 'description' => 'Buku pengetahuan dan referensi'],
            ['name' => 'Teknologi', 'description' => 'Buku tentang teknologi dan komputer'],
            ['name' => 'Sains', 'description' => 'Buku ilmiah dan sains'],
            ['name' => 'Sejarah', 'description' => 'Buku sejarah dan biografi'],
            ['name' => 'Bisnis', 'description' => 'Buku bisnis dan ekonomi'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Create Sample Books
        $books = [
            [
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'isbn' => '9789793062792',
                'publisher' => 'Bentang Pustaka',
                'year' => 2005,
                'category_id' => 1,
                'total_copies' => 5,
                'available_copies' => 5,
                'description' => 'Novel inspiratif tentang perjuangan anak-anak Belitung untuk mendapatkan pendidikan.',
                'location' => 'Rak A-01',
            ],
            [
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'isbn' => '9789799731234',
                'publisher' => 'Hasta Mitra',
                'year' => 1980,
                'category_id' => 1,
                'total_copies' => 3,
                'available_copies' => 3,
                'description' => 'Novel klasik Indonesia yang mengisahkan perjuangan melawan kolonialisme.',
                'location' => 'Rak A-02',
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'isbn' => '9780132350884',
                'publisher' => 'Prentice Hall',
                'year' => 2008,
                'category_id' => 3,
                'total_copies' => 4,
                'available_copies' => 4,
                'description' => 'Buku panduan menulis kode yang bersih dan mudah dipelihara.',
                'location' => 'Rak C-05',
            ],
            [
                'title' => 'Sapiens: A Brief History of Humankind',
                'author' => 'Yuval Noah Harari',
                'isbn' => '9780062316097',
                'publisher' => 'Harper',
                'year' => 2015,
                'category_id' => 4,
                'total_copies' => 3,
                'available_copies' => 3,
                'description' => 'Sejarah singkat umat manusia dari zaman purba hingga modern.',
                'location' => 'Rak D-03',
            ],
            [
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'isbn' => '9780735211292',
                'publisher' => 'Avery',
                'year' => 2018,
                'category_id' => 6,
                'total_copies' => 6,
                'available_copies' => 6,
                'description' => 'Panduan membangun kebiasaan baik dan menghilangkan kebiasaan buruk.',
                'location' => 'Rak F-02',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}