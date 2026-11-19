<?php

namespace App\Http\Controllers;

use App\Models\GobogStock;
use App\Models\GobogTransaction;
use App\Models\Setting;
use App\Models\Tenan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengembalianController extends Controller
{
    public function index(): View
    {
        $stock = GobogStock::current();
        $harga = Setting::hargaGobog();
        $persenAdmin = Setting::persenAdmin();
        $persenTenan = Setting::persenTenan();
        $tenans = Tenan::orderBy('nama')->get();

        $riwayatHariIni = GobogTransaction::with(['user', 'tenan'])
            ->where('tipe', 'pengembalian')
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $ringkasanHariIni = GobogTransaction::where('tipe', 'pengembalian')
            ->whereDate('created_at', today())
            ->selectRaw('COALESCE(SUM(jumlah), 0) as total_jumlah, COALESCE(SUM(total_nominal), 0) as total_nominal')
            ->first();

        return view('admin.pengembalian.index', compact(
            'stock',
            'harga',
            'persenAdmin',
            'persenTenan',
            'tenans',
            'riwayatHariIni',
            'ringkasanHariIni',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tenan_id' => ['required', 'exists:tenans,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'tenan_id.required' => 'Tenan wajib dipilih.',
            'tenan_id.exists' => 'Tenan yang dipilih tidak valid.',
            'jumlah.required' => 'Jumlah Gobog wajib diisi.',
            'jumlah.integer' => 'Jumlah Gobog harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah Gobog minimal 1.',
        ]);

        $jumlah = (int) $validated['jumlah'];
        $harga = Setting::hargaGobog();
        $tenan = Tenan::findOrFail($validated['tenan_id']);
        $berhasil = false;
        $beredarSaatIni = 0;

        DB::transaction(function () use ($jumlah, $harga, $tenan, $validated, &$berhasil, &$beredarSaatIni): void {
            $stock = GobogStock::currentForUpdate();
            $beredarSaatIni = $stock->stok_beredar;

            if ($jumlah > $stock->stok_beredar) {
                return;
            }

            $stock->update([
                'stok_tersedia' => $stock->stok_tersedia + $jumlah,
                'stok_beredar' => $stock->stok_beredar - $jumlah,
            ]);

            GobogTransaction::create([
                'user_id' => auth()->id(),
                'tenan_id' => $tenan->id,
                'tipe' => 'pengembalian',
                'jumlah' => $jumlah,
                'harga_satuan' => $harga,
                'total_nominal' => $jumlah * $harga,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            $berhasil = true;
        });

        if (! $berhasil) {
            return redirect()->route('admin.pengembalian.index')
                ->withInput()
                ->with('error', 'Pengembalian ditolak: jumlah melebihi Gobog yang sedang beredar saat ini ('.number_format($beredarSaatIni, 0, ',', '.').' Gobog).');
        }

        $totalKotor = $jumlah * $harga;
        $persenTenan = Setting::persenTenan();
        $bersihTenan = $totalKotor * ($persenTenan / 100);

        return redirect()->route('admin.pengembalian.index')
            ->with('success', 'Pengembalian '.number_format($jumlah, 0, ',', '.').' Gobog dari '.$tenan->nama.' dicatat. Kotor: Rp '.number_format($totalKotor, 0, ',', '.').' | Bersih Tenan ('.$persenTenan.'%): Rp '.number_format($bersihTenan, 0, ',', '.').'.');
    }
}
