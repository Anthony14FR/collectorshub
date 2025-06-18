<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PromoCode extends Model
{
    use HasFactory;

    protected $table = 'promo_codes';

    protected $fillable = [
        'code',
        'is_active',
        'expires_at',
        'is_global',
        'cash'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_global' => 'boolean',
        'expires_at' => 'datetime',
        'cash' => 'integer',
    ];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'promo_code_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'promo_code_users')
            ->withPivot('is_used', 'used_at')
            ->withTimestamps();
    }
}
