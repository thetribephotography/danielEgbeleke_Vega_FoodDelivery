<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menue extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'description',
        'status' //active, inactive
    ];

    public function foods(): BelongsToMany
    {
        return $this->belongsToMany(Food::class, 'menue_food');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}
