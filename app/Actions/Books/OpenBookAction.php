<?php

namespace App\Actions\Books;

use App\Models\BookPage;
use App\Models\UserBook;
use App\Repositories\Contracts\BookRepositoryInterface;
use App\Services\PageCacheService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OpenBookAction
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
        private readonly PageCacheService $cacheService,
    ) {}

    public function handle(int $userId, int $bookId, int $fontSize): array
    {
        $this->bookRepository->findByIdOrFail($bookId);

        $userBook = UserBook::query()
            ->where('user_id', $userId)
            ->where('book_id', $bookId)
            ->first();

        if (! $userBook) {
            throw new NotFoundHttpException(
                'Book is not in user library.'
            );
        }

        $userBook->update([
            'font_size' => $fontSize,
            'is_active' => true,
        ]);

        $page = $userBook->lastPage;

        if (! $page) {
            $page = BookPage::query()
                ->where('book_id', $bookId)
                ->orderBy('page_number')
                ->firstOrFail();

            $userBook->update([
                'last_page_id' => $page->id,
            ]);
        }

        $this->cacheService->loadAdjacentPages(
            $bookId,
            $page->page_number
        );

        return [
            'book_id' => $bookId,
            'page_number' => $page->page_number,
            'content' => $page->content,
            'font_size' => $fontSize,
        ];
    }
}
