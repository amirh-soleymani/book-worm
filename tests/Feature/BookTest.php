<?php

use App\Models\Book;
use App\Models\BookPage;
use App\Models\User;
use App\Models\UserBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

it('can add a book to library', function () {
    // Arrange
    $user = User::factory()->create();
    $book = Book::factory()->create();

    // Act
    $response = $this->actingAs($user)
        ->postJson("/api/books/{$book->id}/add-to-library");

    // Assert
    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'book_id',
                'user_id',
                'font_size',
                'is_active',
            ],
        ]);

    $this->assertDatabaseHas('user_books', [
        'user_id' => $user->id,
        'book_id' => $book->id,
    ]);
});

it('does not create duplicate library records', function () {
    // Arrange
    $user = User::factory()->create();
    $book = Book::factory()->create();

    // Act
    $this->actingAs($user)
        ->postJson("/api/books/{$book->id}/add-to-library");

    $this->actingAs($user)
        ->postJson("/api/books/{$book->id}/add-to-library");

    // Assert
    expect(
        UserBook::query()->count()
    )->toBe(1);
});

it('returns 404 when adding a non existing book', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/books/999999/add-to-library');

    // Assert
    $response->assertNotFound();
});

it('can open a book', function () {
    // Arrange
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $page = BookPage::factory()->create([
        'book_id' => $book->id,
        'page_number' => 1,
    ]);

    UserBook::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
    ]);

    // Act
    $response = $this->actingAs($user)
        ->postJson("/api/books/{$book->id}/open", [
            'font_size' => 18,
        ]);

    // Assert
    $response->assertOk()
        ->assertJsonPath('data.page_number', $page->page_number)
        ->assertJsonPath('data.font_size', 18);
});

it('saves font size when opening a book', function () {
    // Arrange
    $user = User::factory()->create();
    $book = Book::factory()->create();

    BookPage::factory()->create([
        'book_id' => $book->id,
        'page_number' => 1,
    ]);

    $userBook = UserBook::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
    ]);

    // Act
    $this->actingAs($user)
        ->postJson("/api/books/{$book->id}/open", [
            'font_size' => 22,
        ]);

    // Assert
    expect(
        $userBook->fresh()->font_size
    )->toBe(22);
});

it('marks a book as active when opened', function () {
    // Arrange
    $user = User::factory()->create();
    $book = Book::factory()->create();

    BookPage::factory()->create([
        'book_id' => $book->id,
        'page_number' => 1,
    ]);

    $userBook = UserBook::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'is_active' => false,
    ]);

    // Act
    $this->actingAs($user)
        ->postJson("/api/books/{$book->id}/open", [
            'font_size' => 18,
        ]);

    // Assert
    expect(
        $userBook->fresh()->is_active
    )->toBeTrue();
});

it('sets the first page as last page when opening a book for the first time', function () {
    // Arrange
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $firstPage = BookPage::factory()->create([
        'book_id' => $book->id,
        'page_number' => 1,
    ]);

    $userBook = UserBook::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'last_page_id' => null,
    ]);

    // Act
    $this->actingAs($user)
        ->postJson("/api/books/{$book->id}/open", [
            'font_size' => 18,
        ]);

    // Assert
    expect(
        $userBook->fresh()->last_page_id
    )->toBe($firstPage->id);
});

it('can turn to the next page', function () {
    // Arrange
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $page1 = BookPage::factory()->create([
        'book_id' => $book->id,
        'page_number' => 1,
    ]);

    $page2 = BookPage::factory()->create([
        'book_id' => $book->id,
        'page_number' => 2,
    ]);

    UserBook::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'last_page_id' => $page1->id,
        'font_size' => 20,
    ]);

    // Act
    $response = $this->actingAs($user)
        ->postJson("/api/books/{$book->id}/turn-page", [
            'direction' => 'next',
        ]);

    // Assert
    $response->assertOk()
        ->assertJsonPath('data.page_number', 2)
        ->assertJsonPath('data.font_size', 20);
});

it('can turn to previous page', function () {
    // Arrange
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $page1 = BookPage::factory()->create([
        'book_id' => $book->id,
        'page_number' => 1,
    ]);

    $page2 = BookPage::factory()->create([
        'book_id' => $book->id,
        'page_number' => 2,
    ]);

    UserBook::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'last_page_id' => $page2->id,
    ]);

    // Act & Assert
    $this->actingAs($user)
        ->postJson("/api/books/{$book->id}/turn-page", [
            'direction' => 'previous',
        ])
        ->assertOk()
        ->assertJsonPath('data.page_number', 1);
});

it('updates last page after turning page', function () {
    // Arrange
    $user = User::factory()->create();
    $book = Book::factory()->create();

    $page1 = BookPage::factory()->create([
        'book_id' => $book->id,
        'page_number' => 1,
    ]);

    $page2 = BookPage::factory()->create([
        'book_id' => $book->id,
        'page_number' => 2,
    ]);

    $userBook = UserBook::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'last_page_id' => $page1->id,
    ]);

    // Act
    $this->actingAs($user)
        ->postJson("/api/books/{$book->id}/turn-page", [
            'direction' => 'next',
        ]);

    // Assert
    expect(
        $userBook->fresh()->last_page_id
    )->toBe($page2->id);
});

it('validates font size', function () {
    // Arrange
    $user = User::factory()->create();
    $book = Book::factory()->create();

    UserBook::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
    ]);

    // Act
    $response = $this->actingAs($user)
        ->postJson("/api/books/{$book->id}/open", [
            'font_size' => 100,
        ]);

    // Assert
    $response->assertUnprocessable();
});

it('validates direction field', function () {
    // Arrange
    $user = User::factory()->create();
    $book = Book::factory()->create();

    // Act
    $response = $this->actingAs($user)
        ->postJson("/api/books/{$book->id}/turn-page", [
            'direction' => 'invalid',
        ]);

    // Assert
    $response->assertUnprocessable();
});

it('loads adjacent pages cache when opening book', function () {
    // Arrange
    Cache::flush();
    $user = User::factory()->create();
    $book = Book::factory()->create();

    BookPage::factory()->create([
        'book_id' => $book->id,
        'page_number' => 1,
    ]);

    $bookPage = BookPage::factory()->create([
        'book_id' => $book->id,
        'page_number' => 2,
    ]);

    UserBook::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
    ]);

    $this->actingAs($user)
        ->postJson("/api/books/{$book->id}/open", [
            'font_size' => 16,
        ]);

    expect(
        Cache::has("book:{$book->id}:page:2")
    )->toBeTrue();
});
