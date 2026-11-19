<?php

namespace Database\Seeders;

use App\Models\GobogStock;
use App\Models\HargaHistory;
use App\Models\Setting;
use App\Models\Tenan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('harga_gobog', '5000');
        Setting::set('persen_admin', '20');
        Setting::set('persen_tenan', '80');

        if (HargaHistory::count() === 0) {
            HargaHistory::create([
                'harga' => 5000,
                'keterangan' => 'Harga awal sistem',
            ]);
        }

        if (GobogStock::count() === 0) {
            GobogStock::create([
                'total_stok' => 12000,
                'stok_tersedia' => 12000,
                'stok_beredar' => 0,
            ]);
        }

        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator',
                'password' => Hash::make('admin123'),
            ]
        );

        foreach (['Warung Makan Bu Sari', 'Toko Oleh-oleh Pak Budi', 'Kedai Minuman Segar'] as $namaTenan) {
            Tenan::firstOrCreate(['nama' => $namaTenan]);
        }
    }
}
