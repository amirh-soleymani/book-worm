<?php

namespace App\Actions\Books;

use App\Models\UserBook;
use App\Repositories\Contracts\BookRepositoryInterface;

class AddBookToLibraryAction
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
    ) {}

    public function handle(int $userId, int $bookId): UserBook
    {
        $this->bookRepository->findByIdOrFail($bookId);

        return UserBook::query()->firstOrCreate(
            [
                'user_id' => $userId,
                'book_id' => $bookId,
            ],
            [
                'font_size' => 16,
                'is_active' => false,
                'last_page_id' => null,
            ]
        );
    }
}
