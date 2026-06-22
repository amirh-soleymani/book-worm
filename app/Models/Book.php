<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property positive-int $id
 * @property string $title
 * @property string $author
 * @property int $publish_year
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Fillable(['title', 'author', 'publish_year'])]
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    public function pages(): HasMany
    {
        return $this->hasMany(BookPage::class);
    }

    public function userBooks(): HasMany
    {
        return $this->hasMany(UserBook::class);
    }
}
