<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
    ];


    protected $fillable = [
        'order_id',
        'food_id',
        'user_id',
        'menue_id',
        'quantity',

    ];


    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function food(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Food::class, 'food_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function menue(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Menue::class, 'menue_id');
    }
}
