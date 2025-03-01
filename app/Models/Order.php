<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
    ];

    protected $fillable = [
        'resturant_id',
        'user_id',
        'order_number',
        'order_status', // pending, completed, cancelled
        'expires_at',
    ];

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function resturant(): BelongsTo
    {
        return $this->belongsTo(Resturant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
