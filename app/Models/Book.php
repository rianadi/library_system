<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
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