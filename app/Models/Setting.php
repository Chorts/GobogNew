<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::find($key)?->value ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }

    public static function hargaGobog(): float
    {
        return (float) static::get('harga_gobog', 5000);
    }

    public static function persenAdmin(): float
    {
        return (float) static::get('persen_admin', 20);
    }

    public static function persenTenan(): float
    {
        return (float) static::get('persen_tenan', 80);
    }
}
