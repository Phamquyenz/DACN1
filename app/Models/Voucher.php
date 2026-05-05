<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'type', 'discount_value', 'max_discount', 'min_order_value', 'usage_limit', 'used_count', 'is_for_new_user', 'expires_at', 'reward_condition_amount', 'is_public'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_voucher')->withPivot('is_used', 'used_at')->withTimestamps();
    }
}
