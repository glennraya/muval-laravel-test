<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Automatically set the user_id field to the currently authenticated user.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (auth()->check()) {
                $task->user_id = auth()->id();
            }
        });
    }
}
