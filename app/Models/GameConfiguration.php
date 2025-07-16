<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameConfiguration extends Model
{
    protected $fillable = [
        'category',
        'key',
        'value',
        'description'
    ];

    protected $casts = [
        'value' => 'array'
    ];

    public static function getValue(string $category, string $key, mixed $default = null): mixed
    {
        $config = static::where('category', $category)
            ->where('key', $key)
            ->first();

        return $config ? $config->value : $default;
    }

    public static function setValue(string $category, string $key, mixed $value, ?string $description = null): void
    {
        static::updateOrCreate(
            ['category' => $category, 'key' => $key],
            ['value' => $value, 'description' => $description]
        );
    }
}
