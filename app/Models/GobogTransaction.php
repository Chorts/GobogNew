<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GobogTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'tenan_id',
        'tipe',
        'jumlah',
        'harga_satuan',
        'total_nominal',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'harga_satuan' => 'float',
            'total_nominal' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tenan(): BelongsTo
    {
        return $this->belongsTo(Tenan::class, 'tenan_id');
    }
}
