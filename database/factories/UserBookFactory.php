<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookPage;
use App\Models\User;
use App\Models\UserBook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserBook>
 */
class UserBookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'last_page_id' => BookPage::factory(),
            'font_size' => $this->faker->numberBetween(10, 40),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
