<?php

namespace App\Http\Controllers;

use App\Models\GobogStock;
use App\Models\GobogTransaction;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenjualanController extends Controller
{
    public function index(): View
    {
        $stock = GobogStock::current();
        $harga = Setting::hargaGobog();

        $riwayatHariIni = GobogTransaction::with('user')
            ->where('tipe', 'penjualan')
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $ringkasanHariIni = GobogTransaction::where('tipe', 'penjualan')
            ->whereDate('created_at', today())
            ->selectRaw('COALESCE(SUM(jumlah), 0) as total_jumlah, COALESCE(SUM(total_nominal), 0) as total_nominal')
            ->first();

        return view('admin.penjualan.index', compact(
            'stock',
            'harga',
            'riwayatHariIni',
            'ringkasanHariIni',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'jumlah.required' => 'Jumlah Gobog wajib diisi.',
            'jumlah.integer' => 'Jumlah Gobog harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah Gobog minimal 1.',
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
                'stok_tersedia' => $stock->stok_tersedia - $jumlah,
                'stok_beredar' => $stock->stok_beredar + $jumlah,
            ]);

            GobogTransaction::create([
                'user_id' => auth()->id(),
                'tenan_id' => null,
                'tipe' => 'penjualan',
                'jumlah' => $jumlah,
                'harga_satuan' => $harga,
                'total_nominal' => $jumlah * $harga,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            $berhasil = true;
        });

        if (! $berhasil) {
            return redirect()->route('admin.penjualan.index')
                ->withInput()
                ->with('error', 'Penjualan ditolak: jumlah melebihi stok Gobog yang tersedia saat ini ('.number_format($tersediaSaatIni, 0, ',', '.').' Gobog).');
        }

        $totalNominal = $jumlah * $harga;

        return redirect()->route('admin.penjualan.index')
            ->with('success', 'Penjualan '.number_format($jumlah, 0, ',', '.').' Gobog (Rp '.number_format($totalNominal, 0, ',', '.').') berhasil dicatat.');
    }
}
