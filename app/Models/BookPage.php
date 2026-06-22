<?php

namespace App\Models;

use Database\Factories\BookPageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property positive-int $id
 * @property Book $book_id
 * @property int $page_number
 * @property string $content
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable(['book_id', 'page_number', 'content'])]
class BookPage extends Model
{
    /** @use HasFactory<BookPageFactory> */
    use HasFactory;

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function readers(): HasMany
    {
        return $this->hasMany(UserBook::class, 'last_page_id');
    }
}
