<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resturant extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
    ];


    protected $fillable = [
        'name',
        'longitude',
        'latitude',
        'phone',
        'is_booked',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }


}

