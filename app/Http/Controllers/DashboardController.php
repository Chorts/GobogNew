<?php

namespace App\Http\Controllers;

use App\Models\GobogStock;
use App\Models\GobogTransaction;
use App\Models\Setting;
use App\Models\Tenan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stock = GobogStock::current();
        $harga = Setting::hargaGobog();
        $persenAdmin = Setting::persenAdmin();
        $persenTenan = Setting::persenTenan();

        $penjualanHariIni = GobogTransaction::where('tipe', 'penjualan')
            ->whereDate('created_at', today())
            ->selectRaw('COALESCE(SUM(jumlah), 0) as total_jumlah, COALESCE(SUM(total_nominal), 0) as total_nominal')
            ->first();

        $pengembalianHariIni = GobogTransaction::where('tipe', 'pengembalian')
            ->whereDate('created_at', today())
            ->selectRaw('COALESCE(SUM(jumlah), 0) as total_jumlah, COALESCE(SUM(total_nominal), 0) as total_nominal')
            ->first();

        $totalTenan = Tenan::count();

        $transaksiTerakhir = GobogTransaction::with(['user', 'tenan'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', compact(
            'stock',
            'harga',
            'persenAdmin',
            'persenTenan',
            'penjualanHariIni',
            'pengembalianHariIni',
            'totalTenan',
            'transaksiTerakhir',
        ));
    }
}
