<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Food extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'price',
        'description',
        'overal_rating',// avearge rating of the food
        'status' // true - available, false - not available
    ];

    public function menues(): BelongsToMany
    {
        return $this->belongsToMany(Menue::class, 'menue_food');
    }


    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'food_id');
    }



}
