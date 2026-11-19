<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Gobog</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #222; margin: 18px; line-height: 1.4; }
        h1 { text-align: center; font-size: 16px; margin: 0 0 4px; font-weight: bold; }
        .sub { text-align: center; font-size: 10px; color: #666; margin: 0 0 16px; }
        .summary-box { border: 1px solid #ddd; padding: 10px 14px; margin-bottom: 14px; background: #fafafa; }
        .summary-top { text-align: center; border-bottom: 1px solid #e5e5e5; padding-bottom: 8px; margin-bottom: 8px; }
        .summary-top .label { font-size: 10px; text-transform: uppercase; color: #666; }
        .summary-top .value { font-size: 18px; font-weight: bold; color: #111; }
        .summary-grid { width: 100%; border-collapse: collapse; margin: 0; }
        .summary-grid td { border: none; text-align: center; width: 50%; padding: 4px; }
        .summary-grid .label-share { font-size: 10px; color: #666; }
        .summary-grid .value-share { font-size: 13px; font-weight: bold; color: #111; }
        h2 { font-size: 11px; background: #f2f2f2; padding: 5px 8px; margin: 16px 0 6px; border-left: 3px solid #555; text-transform: uppercase; color: #333; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 10px; }
        table.data th { background: #181c24; color: #ffffff; padding: 6px 7px; text-align: left; font-weight: bold; border-top: 1px solid #181c24; border-bottom: 1.5px solid #181c24; }
        table.data td { border-bottom: 1px solid #e5e5e5; padding: 5px 7px; }
        table.data tr:nth-child(even) td { background: #fafafa; }
        table.data tfoot td { font-weight: bold; background: #f4f4f4; border-top: 1.5px solid #666; border-bottom: 1px solid #ccc; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .masuk { color: #198754; font-weight: bold; }
        .keluar { color: #dc3545; font-weight: bold; }
        .no-data { color: #888; font-style: italic; text-align: center; padding: 12px !important; }
        .footer { text-align: center; font-size: 9px; color: #888; margin-top: 24px; border-top: 1px solid #ddd; padding-top: 6px; }
    </style>
</head>
<body>
    <h1>Laporan Gobog &mdash; Pasar Preng Sewu</h1>
    <p class="sub">
        Periode: {{ $periodeLabel }} |
        Tenan: {{ $selectedTenan ? $selectedTenan->nama : 'Semua Tenan' }} |
        Bagi Hasil: Tenan {{ $persenTenan }}% / Admin {{ $persenAdmin }}% |
        Dicetak: {{ now()->format('d/m/Y H:i') }}
    </p>

    <div class="summary-box">
        <div class="summary-top">
            <div class="label">Total Pendapatan Kotor ({{ number_format($totalGobogKembali, 0, ',', '.') }} Gobog Masuk)</div>
            <div class="value">Rp {{ number_format($totalKotor, 0, ',', '.') }}</div>
        </div>
        <table class="summary-grid">
            <tr>
                <td>
                    <div class="label-share">Pendapatan Bersih Tenan ({{ $persenTenan }}%)</div>
                    <div class="value-share" style="color: #198754;">Rp {{ number_format($totalBersihTenan, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label-share">Bagian Admin ({{ $persenAdmin }}%)</div>
                    <div class="value-share" style="color: #0d6efd;">Rp {{ number_format($totalBagianAdmin, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <h2>Rekap Bagi Hasil per Tenan</h2>
    <table class="data">
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th>Tenan</th>
                <th class="text-center">Transaksi</th>
                <th class="text-right">Total Gobog</th>
                <th class="text-right">Total Kotor</th>
                <th class="text-right">Bersih Tenan ({{ $persenTenan }}%)</th>
                <th class="text-right">Bagian Admin ({{ $persenAdmin }}%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapPerTenan as $row)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $row['tenan_nama'] }}</td>
                    <td class="text-center">{{ $row['jumlah_transaksi'] }}x</td>
                    <td class="text-right masuk">+{{ number_format($row['total_gobog'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row['total_kotor'], 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #198754; font-weight: bold;">Rp {{ number_format($row['pendapatan_bersih'], 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #0d6efd; font-weight: bold;">Rp {{ number_format($row['bagian_admin'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="no-data">Tidak ada data pengembalian.</td></tr>
            @endforelse
        </tbody>
        @if($rekapPerTenan->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="3" class="text-right">Grand Total:</td>
                <td class="text-right masuk">+{{ number_format($totalGobogKembali, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalKotor, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #198754;">Rp {{ number_format($totalBersihTenan, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #0d6efd;">Rp {{ number_format($totalBagianAdmin, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <h2>Detail Riwayat Pengembalian (Masuk)</h2>
    <table class="data">
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th>Tenan</th>
                <th class="text-right">Jumlah</th>
                <th class="text-right">Harga / Gobog</th>
                <th class="text-right">Kotor</th>
                <th class="text-right">Bersih ({{ $persenTenan }}%)</th>
                <th class="text-right">Admin ({{ $persenAdmin }}%)</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengembalians as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->tenan?->nama ?? '-' }}</td>
                    <td class="text-right masuk">+{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_nominal, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #198754;">Rp {{ number_format($item->total_nominal * ($persenTenan / 100), 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #0d6efd;">Rp {{ number_format($item->total_nominal * ($persenAdmin / 100), 0, ',', '.') }}</td>
                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="no-data">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if(! $selectedTenan)
    <h2>Detail Riwayat Penjualan Admin (Keluar)</h2>
    <table class="data">
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th>Admin</th>
                <th class="text-right">Jumlah</th>
                <th class="text-right">Harga / Gobog</th>
                <th class="text-right">Total (Rp)</th>
                <th>Keterangan</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penjualans as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->user->nama }}</td>
                    <td class="text-right keluar">-{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_nominal, 0, ',', '.') }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="no-data">Tidak ada data penjualan.</td></tr>
            @endforelse
        </tbody>
        @if($penjualans->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="2" class="text-right">Total Penjualan:</td>
                <td class="text-right keluar">-{{ number_format($totalGobogTerjual, 0, ',', '.') }}</td>
                <td></td>
                <td colspan="3">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <h2>Posisi Stok Gobog Saat Ini</h2>
    <table class="data">
        <thead>
            <tr>
                <th class="text-center">Total Keseluruhan</th>
                <th class="text-center">Tersedia di Kasir</th>
                <th class="text-center">Sedang Beredar</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ number_format($stock->total_stok, 0, ',', '.') }} Gobog</td>
                <td class="text-center">{{ number_format($stock->stok_tersedia, 0, ',', '.') }} Gobog</td>
                <td class="text-center">{{ number_format($stock->stok_beredar, 0, ',', '.') }} Gobog</td>
            </tr>
        </tbody>
    </table>
    @endif

    <div class="footer">Sistem Gobog &mdash; Pasar Preng Sewu</div>
</body>
</html>
