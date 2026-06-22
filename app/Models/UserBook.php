<?php

namespace App\Models;

use Database\Factories\UserBookFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property positive-int $id
 * @property User $user_id
 * @property Book $book_id
 * @property BookPage $last_page_id
 * @property int $font_size
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable(['user_id', 'book_id', 'last_page_id', 'font_size', 'is_active'])]
class UserBook extends Model
{
    /** @use HasFactory<UserBookFactory> */
    use HasFactory;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function lastPage(): BelongsTo
    {
        return $this->belongsTo(BookPage::class, 'last_page_id');
    }
}
