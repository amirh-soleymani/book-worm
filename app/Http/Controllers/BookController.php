<?php

namespace App\Http\Controllers;

use App\Actions\Books\AddBookToLibraryAction;
use App\Actions\Books\OpenBookAction;
use App\Actions\Books\TurnPageAction;
use App\Http\Requests\OpenBookRequest;
use App\Http\Requests\TurnPageRequest;
use App\Http\Resources\PageResource;
use App\Http\Resources\UserBookResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class BookController extends Controller
{
    public function addToLibrary(int $bookId, AddBookToLibraryAction $action): JsonResponse
    {
        $userBook = $action->handle(auth()->id(), $bookId);

        return Response::success(
            data: new UserBookResource($userBook),
            message: 'Book successfully added to library.',
        );
    }

    public function open(OpenBookRequest $openBookRequest, int $bookId, OpenBookAction $action): JsonResponse
    {
        $page = $action->handle(auth()->id(), $bookId, $openBookRequest->integer('font_size'));

        return Response::success(
            data: new PageResource($page),
            message: 'Book successfully opened.',
        );
    }

    public function turnPage(TurnPageRequest $request, int $bookId, TurnPageAction $action): JsonResponse
    {
        $page = $action->handle(auth()->id(), $bookId, $request->validated('direction'));

        return Response::success(
            data: new PageResource($page),
            message: 'Book turned page.',
        );
    }
}
