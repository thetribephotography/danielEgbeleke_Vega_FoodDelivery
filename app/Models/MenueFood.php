<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenueFood extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
    ];

    protected $fillable = [
        'menue_id',
        'food_id'
    ];

    public function menue(): BelongsTo
    {
        return $this->belongsTo(Menue::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }
}
