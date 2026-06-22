<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookRepository implements BookRepositoryInterface
{
    public function findById(int $bookId): ?Book
    {
        return Book::query()->find($bookId);
    }

    public function findByIdOrFail(int $bookId): Book
    {
        $book = $this->findById($bookId);

        if (! $book) {
            throw new NotFoundHttpException('Book not found.');
        }

        return $book;
    }
}
