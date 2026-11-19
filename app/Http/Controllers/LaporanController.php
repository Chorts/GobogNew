<?php

namespace App\Http\Controllers;

use App\Models\GobogStock;
use App\Models\GobogTransaction;
use App\Models\Setting;
use App\Models\Tenan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $data = $this->buildReportData($request);
        $data['tenans'] = Tenan::orderBy('nama')->get();

        return view('admin.laporan.index', $data);
    }

    public function exportPdf(Request $request): Response
    {
        $data = $this->buildReportData($request);

        $pdf = Pdf::loadView('admin.laporan.pdf', $data)->setPaper('a4', 'portrait');

        $slugTenan = $data['selectedTenan']
            ? '-'.Str::slug($data['selectedTenan']->nama)
            : '';

        return $pdf->download('laporan-gobog-'.$data['filter'].$slugTenan.'-'.now()->format('Ymd').'.pdf');
    }

    private function buildReportData(Request $request): array
    {
        $filter = (string) $request->input('filter', 'harian');
        if (! in_array($filter, ['harian', 'mingguan', 'bulanan', 'tahunan'], true)) {
            $filter = 'harian';
        }

        $tanggal = (string) $request->input('tanggal', now()->toDateString());
        $bulan = max(1, min(12, (int) $request->input('bulan', now()->month)));
        $tahun = (int) $request->input('tahun', now()->year);
        $tenanId = $request->filled('tenan_id') ? (int) $request->input('tenan_id') : null;

        $selectedTenan = $tenanId ? Tenan::find($tenanId) : null;

        $applyDateFilter = function (Builder $query) use ($filter, $tanggal, $bulan, $tahun): Builder {
            return match ($filter) {
                'harian' => $query->whereDate('created_at', $tanggal),
                'mingguan' => $query->whereBetween('created_at', [
                    Carbon::parse($tanggal)->startOfWeek(),
                    Carbon::parse($tanggal)->endOfWeek(),
                ]),
                'bulanan' => $query->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun),
                'tahunan' => $query->whereYear('created_at', $tahun),
            };
        };

        $periodeLabel = match ($filter) {
            'harian' => 'Harian — '.Carbon::parse($tanggal)->format('d/m/Y'),
            'mingguan' => 'Mingguan — '.Carbon::parse($tanggal)->startOfWeek()->format('d/m/Y').' s/d '.Carbon::parse($tanggal)->endOfWeek()->format('d/m/Y'),
            'bulanan' => 'Bulanan — '.Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y'),
            'tahunan' => 'Tahunan — '.$tahun,
        };

        $persenAdmin = Setting::persenAdmin();
        $persenTenan = Setting::persenTenan();
        $harga = Setting::hargaGobog();

        $pengembalianQuery = $applyDateFilter(
            GobogTransaction::with(['user', 'tenan'])->where('tipe', 'pengembalian')
        );

        if ($selectedTenan) {
            $pengembalianQuery->where('tenan_id', $selectedTenan->id);
        }

        $pengembalians = $pengembalianQuery->latest()->get();

        $penjualans = $applyDateFilter(
            GobogTransaction::with('user')->where('tipe', 'penjualan')
        )->latest()->get();

        $totalGobogKembali = (int) $pengembalians->sum('jumlah');
        $totalKotor = (float) $pengembalians->sum('total_nominal');
        $totalBersihTenan = $totalKotor * ($persenTenan / 100);
        $totalBagianAdmin = $totalKotor * ($persenAdmin / 100);

        $rekapPerTenan = $pengembalians
            ->groupBy('tenan_id')
            ->map(function ($items) use ($persenTenan, $persenAdmin): array {
                $first = $items->first();
                $kotor = (float) $items->sum('total_nominal');

                return [
                    'tenan_id' => $first->tenan_id,
                    'tenan_nama' => $first->tenan?->nama ?? '-',
                    'jumlah_transaksi' => $items->count(),
                    'total_gobog' => (int) $items->sum('jumlah'),
                    'total_kotor' => $kotor,
                    'pendapatan_bersih' => $kotor * ($persenTenan / 100),
                    'bagian_admin' => $kotor * ($persenAdmin / 100),
                ];
            })
            ->sortBy('tenan_nama')
            ->values();

        $totalGobogTerjual = (int) $penjualans->sum('jumlah');
        $totalPenjualan = (float) $penjualans->sum('total_nominal');

        $stock = GobogStock::current();

        return [
            'filter' => $filter,
            'tanggal' => $tanggal,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'tenanId' => $selectedTenan?->id,
            'selectedTenan' => $selectedTenan,
            'periodeLabel' => $periodeLabel,
            'persenAdmin' => $persenAdmin,
            'persenTenan' => $persenTenan,
            'harga' => $harga,
            'stock' => $stock,
            'pengembalians' => $pengembalians,
            'penjualans' => $penjualans,
            'rekapPerTenan' => $rekapPerTenan,
            'totalGobogKembali' => $totalGobogKembali,
            'totalKotor' => $totalKotor,
            'totalBersihTenan' => $totalBersihTenan,
            'totalBagianAdmin' => $totalBagianAdmin,
            'totalGobogTerjual' => $totalGobogTerjual,
            'totalPenjualan' => $totalPenjualan,
        ];
    }
}
