<?php

namespace App\Actions\Books;

use App\Models\UserBook;
use App\Repositories\Contracts\BookRepositoryInterface;
use App\Services\PageCacheService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TurnPageAction
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository,
        private readonly PageCacheService $cacheService,
    ) {}

    public function handle(int $userId, int $bookId, string $direction): array
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

        $currentPage = $userBook->lastPage;

        if (! $currentPage) {
            throw new NotFoundHttpException(
                'Book has not been opened yet.'
            );
        }

        $targetPageNumber = match ($direction) {
            'next' => $currentPage->page_number + 1,
            'previous' => max(
                1,
                $currentPage->page_number - 1
            ),
        };

        $page = $this->cacheService->getPage($bookId, $targetPageNumber);

        if (! $page) {
            $page = $currentPage;
        }

        $userBook->update([
            'last_page_id' => $page['id'],
        ]);

        $this->cacheService->loadAdjacentPages($bookId, $page['page_number']);

        return [
            'book_id' => $bookId,
            'page_number' => $page['page_number'],
            'content' => $page['content'],
            'font_size' => $userBook->font_size,
        ];
    }
}
