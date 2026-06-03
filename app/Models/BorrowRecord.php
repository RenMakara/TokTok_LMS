<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRecord extends Model
{
    // Primary Key
    protected $primaryKey = 'br_id';
    public $incrementing = true; // since you used $table->id()

    // Fillable columns (must match migration)
    protected $fillable = [
        'book_id',
        'user_id',
        'quantity',
        'borrow_status',
        'check_out_date',
        'check_in_date',
        'penalty',
    ];

    // Relationship with Book (book_id → book_id)
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id', 'book_id');
    }

    // Relationship with User (user_id → user_id)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
