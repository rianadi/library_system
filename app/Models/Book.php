<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_code',
        'title',
        'author',
        'isbn',
        'publisher',
        'year',
        'category_id',
        'total_copies',
        'available_copies',
        'description',
        'cover_image',
        'location',
    ];

    protected $casts = [
        'year' => 'integer',
        'total_copies' => 'integer',
        'available_copies' => 'integer',
    ];

    protected static function booted(): void
    {
        static::created(function (Book $book): void {
            if (! $book->book_code) {
                $book->forceFill([
                    'book_code' => static::codeForId($book->id),
                ])->saveQuietly();
            }
        });

        static::saving(function (Book $book): void {
            if ($book->exists && ! $book->book_code) {
                $book->book_code = static::codeForId($book->id);
            }
        });
    }

    public static function codeForId(int $id): string
    {
        return 'BK'.str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function isAvailable()
    {
        return $this->available_copies > 0;
    }

    public function decreaseAvailableCopies()
    {
        if ($this->available_copies > 0) {
            $this->decrement('available_copies');
        }
    }

    public function increaseAvailableCopies()
    {
        if ($this->available_copies < $this->total_copies) {
            $this->increment('available_copies');
        }
    }
}
