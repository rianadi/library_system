<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'fine_amount',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'approved_at' => 'datetime',
        'fine_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Method untuk cek apakah peminjaman overdue
     */
    public function isOverdue()
    {
        if ($this->return_date) {
            return false;
        }
        return Carbon::parse($this->due_date)->isPast();
    }

    public function calculateFine()
    {
        if ($this->isOverdue()) {
            $daysOverdue = Carbon::parse($this->due_date)->diffInDays(Carbon::today());
            return $daysOverdue * 2000;
        }
        return 0;
    }
}