<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GobogStock extends Model
{
    protected $fillable = [
        'total_stok',
        'stok_tersedia',
        'stok_beredar',
    ];

    protected function casts(): array
    {
        return [
            'total_stok' => 'integer',
            'stok_tersedia' => 'integer',
            'stok_beredar' => 'integer',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'total_stok' => 0,
            'stok_tersedia' => 0,
            'stok_beredar' => 0,
        ]);
    }

    public static function currentForUpdate(): self
    {
        $stock = static::query()->lockForUpdate()->first();

        if (! $stock) {
            return static::create([
                'total_stok' => 0,
                'stok_tersedia' => 0,
                'stok_beredar' => 0,
            ]);
        }

        return $stock;
    }
}
