<?php

namespace App\Services;

use App\Models\BookPage;
use Illuminate\Support\Facades\Cache;

class PageCacheService
{
    private const int TTL = 1800;

    public function getPage(int $bookId, int $pageNumber): ?array
    {
        return Cache::remember(
            $this->key($bookId, $pageNumber),
            self::TTL,
            function () use ($bookId, $pageNumber) {
                $page = BookPage::query()
                    ->where('book_id', $bookId)
                    ->where('page_number', $pageNumber)
                    ->first();

                if (! $page) {
                    return null;
                }

                return [
                    'id' => $page->id,
                    'book_id' => $page->book_id,
                    'page_number' => $page->page_number,
                    'content' => $page->content,
                ];
            }
        );
    }

    public function loadAdjacentPages(
        int $bookId,
        int $pageNumber,
    ): void {
        $this->getPage($bookId, $pageNumber - 1);

        $this->getPage($bookId, $pageNumber + 1);
    }

    private function key(
        int $bookId,
        int $pageNumber,
    ): string {
        return sprintf(
            'book:%s:page:%s',
            $bookId,
            $pageNumber
        );
    }
}
