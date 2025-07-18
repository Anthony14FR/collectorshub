<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const TYPE_ANNOUNCEMENT = 'announcement';
    public const TYPE_MARKETPLACE_BUY = 'marketplace_buy';
    public const TYPE_MARKETPLACE_SELL = 'marketplace_sell';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeAnnouncements($query)
    {
        return $query->where('type', self::TYPE_ANNOUNCEMENT);
    }

    public function scopeMarketplaceHistory($query)
    {
        return $query->whereIn('type', [self::TYPE_MARKETPLACE_BUY, self::TYPE_MARKETPLACE_SELL]);
    }

    public static function createMarketplaceBuy(int $userId, array $data): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_MARKETPLACE_BUY,
            'title' => 'Achat réalisé',
            'message' => "Vous avez acheté {$data['pokemon_name']} pour {$data['price']} coins",
            'data' => $data,
        ]);
    }

    public static function createMarketplaceSell(int $userId, array $data): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_MARKETPLACE_SELL,
            'title' => 'Vente réalisée',
            'message' => "Vous avez vendu {$data['pokemon_name']} pour {$data['price']} coins",
            'data' => $data,
        ]);
    }

    public static function createAnnouncement(int $userId, string $title, string $message, array $data = []): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_ANNOUNCEMENT,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
