<?php

namespace App\Http\Controllers;

use App\Models\HargaHistory;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function index(): View
    {
        $harga = Setting::hargaGobog();
        $persenAdmin = Setting::persenAdmin();
        $persenTenan = Setting::persenTenan();
        $riwayat = HargaHistory::latest()->get();

        return view('admin.pengaturan.index', compact('harga', 'persenAdmin', 'persenTenan', 'riwayat'));
    }

    public function updateHarga(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'harga_gobog' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'harga_gobog.required' => 'Harga Gobog wajib diisi.',
            'harga_gobog.numeric' => 'Harga Gobog harus berupa angka.',
            'harga_gobog.min' => 'Harga Gobog tidak boleh negatif.',
        ]);

        $hargaLama = Setting::hargaGobog();
        $hargaBaru = (float) $validated['harga_gobog'];

        if ($hargaBaru !== $hargaLama) {
            HargaHistory::create([
                'harga' => $hargaBaru,
                'keterangan' => $validated['keterangan'] ?: 'Diperbarui oleh admin',
            ]);
        }

        Setting::set('harga_gobog', $hargaBaru);

        return redirect()->route('admin.pengaturan.index')
            ->with('success', 'Harga Gobog diperbarui menjadi Rp '.number_format($hargaBaru, 0, ',', '.').'. Transaksi lama tetap menyimpan harga saat tanggal transaksi.');
    }

    public function updateBagiHasil(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'persen_admin' => ['required', 'numeric', 'min:0', 'max:100'],
            'persen_tenan' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'persen_admin.required' => 'Persentase admin wajib diisi.',
            'persen_admin.numeric' => 'Persentase admin harus berupa angka.',
            'persen_admin.min' => 'Persentase admin minimal 0%.',
            'persen_admin.max' => 'Persentase admin maksimal 100%.',
            'persen_tenan.required' => 'Persentase tenan wajib diisi.',
            'persen_tenan.numeric' => 'Persentase tenan harus berupa angka.',
            'persen_tenan.min' => 'Persentase tenan minimal 0%.',
            'persen_tenan.max' => 'Persentase tenan maksimal 100%.',
        ]);

        $persenAdmin = round((float) $validated['persen_admin'], 2);
        $persenTenan = round((float) $validated['persen_tenan'], 2);

        if (abs(($persenAdmin + $persenTenan) - 100) > 0.01) {
            return back()
                ->withInput()
                ->withErrors(['persen_admin' => 'Total persentase Admin + Tenan harus tepat 100%.']);
        }

        Setting::set('persen_admin', $persenAdmin);
        Setting::set('persen_tenan', $persenTenan);

        return redirect()->route('admin.pengaturan.index')
            ->with('success', "Persentase bagi hasil berhasil disimpan (Admin {$persenAdmin}% / Tenan {$persenTenan}%). Laporan otomatis menyesuaikan.");
    }
}
