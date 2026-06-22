<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookPage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::factory()
            ->count(10)
            ->create()
            ->each(function (Book $book) {
                $pageCount = fake()->numberBetween(50, 200);

                foreach (range(1, $pageCount) as $pageNumber) {
                    BookPage::factory()->create([
                        'book_id' => $book->id,
                        'page_number' => $pageNumber,
                    ]);
                }
            });
    }
}
