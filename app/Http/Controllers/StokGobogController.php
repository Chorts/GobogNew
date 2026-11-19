<?php

namespace App\Http\Controllers;

use App\Models\GobogStock;
use App\Models\GobogTransaction;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StokGobogController extends Controller
{
    public function index(): View
    {
        $stock = GobogStock::current();
        $harga = Setting::hargaGobog();
        $riwayat = GobogTransaction::with('user')
            ->whereIn('tipe', ['tambah_stok', 'kurangi_stok'])
            ->latest()
            ->get();

        return view('admin.stok.index', compact('stock', 'harga', 'riwayat'));
    }

    public function tambah(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1', 'max:1000000'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'jumlah.required' => 'Jumlah Gobog wajib diisi.',
            'jumlah.integer' => 'Jumlah Gobog harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah Gobog minimal 1.',
            'jumlah.max' => 'Jumlah Gobog maksimal 1.000.000 per sekali tambah.',
        ]);

        $jumlah = (int) $validated['jumlah'];
        $harga = Setting::hargaGobog();

        DB::transaction(function () use ($jumlah, $harga, $validated): void {
            $stock = GobogStock::currentForUpdate();
            $stock->update([
                'total_stok' => $stock->total_stok + $jumlah,
                'stok_tersedia' => $stock->stok_tersedia + $jumlah,
            ]);

            GobogTransaction::create([
                'user_id' => auth()->id(),
                'tenan_id' => null,
                'tipe' => 'tambah_stok',
                'jumlah' => $jumlah,
                'harga_satuan' => $harga,
                'total_nominal' => $jumlah * $harga,
                'keterangan' => $validated['keterangan'] ?: 'Penambahan stok Gobog baru',
            ]);
        });

        return redirect()->route('admin.stok.index')
            ->with('success', 'Berhasil menambahkan '.number_format($jumlah, 0, ',', '.').' Gobog ke total stok.');
    }

    public function kurangi(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
            'keterangan' => ['required', 'string', 'max:255'],
        ], [
            'jumlah.required' => 'Jumlah pengurangan wajib diisi.',
            'jumlah.integer' => 'Jumlah pengurangan harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah pengurangan minimal 1.',
            'keterangan.required' => 'Alasan pengurangan stok wajib diisi.',
        ]);

        $jumlah = (int) $validated['jumlah'];
        $harga = Setting::hargaGobog();
        $berhasil = false;
        $tersediaSaatIni = 0;

        DB::transaction(function () use ($jumlah, $harga, $validated, &$berhasil, &$tersediaSaatIni): void {
            $stock = GobogStock::currentForUpdate();
            $tersediaSaatIni = $stock->stok_tersedia;

            if ($jumlah > $stock->stok_tersedia) {
                return;
            }

            $stock->update([
                'total_stok' => $stock->total_stok - $jumlah,
                'stok_tersedia' => $stock->stok_tersedia - $jumlah,
            ]);

            GobogTransaction::create([
                'user_id' => auth()->id(),
                'tenan_id' => null,
                'tipe' => 'kurangi_stok',
                'jumlah' => $jumlah,
                'harga_satuan' => $harga,
                'total_nominal' => $jumlah * $harga,
                'keterangan' => $validated['keterangan'],
            ]);

            $berhasil = true;
        });

        if (! $berhasil) {
            return redirect()->route('admin.stok.index')
                ->withInput()
                ->with('error', 'Pengurangan stok ditolak: jumlah melebihi stok Gobog yang tersedia saat ini ('.number_format($tersediaSaatIni, 0, ',', '.').' Gobog).');
        }

        return redirect()->route('admin.stok.index')
            ->with('success', 'Berhasil mengurangi '.number_format($jumlah, 0, ',', '.').' Gobog dari stok.');
    }
}
