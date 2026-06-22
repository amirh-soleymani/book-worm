<?php

namespace App\Repositories\Contracts;

use App\Models\Book;

interface BookRepositoryInterface
{
    public function findById(int $bookId): ?Book;

    public function findByIdOrFail(int $bookId): Book;
}
