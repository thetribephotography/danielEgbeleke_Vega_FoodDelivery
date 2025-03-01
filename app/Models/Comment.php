<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
    ];

    protected $fillable = [
        'comment',
        'user_id',
        'food_id',
        'rating', // 1, 2, 3, 4, 5
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

}
