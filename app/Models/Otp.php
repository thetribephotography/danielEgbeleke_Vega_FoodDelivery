<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Otp extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
    ];


    protected $fillable = [
        'user_id',
        'email',
        'otp',
        'reason',
        'expires_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::addGlobalScope('user', function ($query) {
            $query->with('user');
        });
    }


    static function generateOtp($user_id, $email, $reason): object|null
    {
        try {

            $old_otp = self::where('user_id', $user_id)->where('email', $email)->where('expires_at', '>', now())->with(['user'])->first();

            if ($old_otp) {
                return $old_otp;
            } else {
                $otp = generateCode();
                $otp_data = self::create([
                    'user_id' => $user_id,
                    'email' => $email,
                    'otp' => $otp,
                    'reason' => $reason,
                    'expires_at' => now()->addMinutes(10),
                ]);

                if ($otp_data) {
                    Log::info("OTP Generated: " . $otp);
                    return $otp_data;
                } else {
                    return null;
                }
            }

        } catch (\Throwable $err) {
            return null;
        }
    }


    static function verifyOtp($user_id, $otp): bool
    {
        $otp = self::where('user_id', $user_id)->where('otp', $otp)->where('expires_at', '>', now())->first();
        if ($otp) {
            // $otp;
            return true;
        }

        return false;
    }
}
