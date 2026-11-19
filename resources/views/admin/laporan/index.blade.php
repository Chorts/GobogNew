@extends('layouts.app')
@section('title', 'Laporan')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h4 class="fw-bold mb-0">Laporan</h4>
    <a href="{{ route('admin.laporan.export-pdf', request()->query()) }}" class="btn btn-dark btn-sm">
        <i class="bi bi-file-pdf me-1"></i>Export PDF
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="row g-2 align-items-end">
            <div class="col-6 col-sm-auto">
                <label class="form-label small text-muted mb-1">Periode</label>
                <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="harian" {{ $filter === 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="mingguan" {{ $filter === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ $filter === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ $filter === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>

            @if(in_array($filter, ['harian', 'mingguan']))
                <div class="col-6 col-sm-auto">
                    <label class="form-label small text-muted mb-1">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ $tanggal }}">
                </div>
            @elseif($filter === 'bulanan')
                <div class="col-6 col-sm-auto">
                    <label class="form-label small text-muted mb-1">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $bulan === $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(2000, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-sm-auto">
                    <label class="form-label small text-muted mb-1">Tahun</label>
                    <input type="number" name="tahun" class="form-control form-control-sm" style="width:90px" value="{{ $tahun }}">
                </div>
            @else
                <div class="col-6 col-sm-auto">
                    <label class="form-label small text-muted mb-1">Tahun</label>
                    <input type="number" name="tahun" class="form-control form-control-sm" style="width:90px" value="{{ $tahun }}">
                </div>
            @endif

            <div class="col-12 col-sm-auto">
                <label class="form-label small text-muted mb-1">Filter Tenan</label>
                <select name="tenan_id" class="form-select form-select-sm">
                    <option value="">Semua Tenan</option>
                    @foreach($tenans as $t)
                        <option value="{{ $t->id }}" {{ $tenanId === $t->id ? 'selected' : '' }}>{{ $t->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-dark btn-sm">
                    <i class="bi bi-search me-1"></i>Tampilkan
                </button>
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm mb-4 border bg-white">
    <div class="card-body py-2 d-flex flex-wrap justify-content-between align-items-center gap-2 small">
        <div>
            <strong>Periode:</strong> {{ $periodeLabel }}
            <span class="text-muted mx-2">|</span>
            <strong>Tenan:</strong> {{ $selectedTenan ? $selectedTenan->nama : 'Semua Tenan' }}
            <span class="text-muted mx-2">|</span>
            <strong>Bagi Hasil Aktif:</strong> Tenan <span class="badge border bg-white text-dark">{{ $persenTenan }}%</span> / Admin <span class="badge border bg-white text-dark">{{ $persenAdmin }}%</span>
        </div>
        <a href="{{ route('admin.pengaturan.index') }}" class="text-dark text-decoration-none fw-semibold">
            <i class="bi bi-sliders me-1"></i>Ubah Persentase
        </a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Ringkasan Pendapatan {{ $selectedTenan ? '— '.$selectedTenan->nama : '(Semua Tenan)' }}</span>
        <span class="badge bg-success">+{{ number_format($totalGobogKembali, 0, ',', '.') }} Gobog Masuk</span>
    </div>
    <div class="card-body">
        <div class="text-center pb-3 mb-3 border-bottom">
            <div class="text-muted small text-uppercase fw-semibold">Total Pendapatan Kotor</div>
            <div class="display-6 fw-bold text-dark">Rp {{ number_format($totalKotor, 0, ',', '.') }}</div>
            <div class="small text-muted">Dihitung dari harga Gobog masing-masing pada tanggal transaksi</div>
        </div>
        <div class="row g-3 text-center">
            <div class="col-12 col-md-6">
                <div class="p-3 rounded bg-success-subtle border border-success-subtle h-100">
                    <div class="text-success-emphasis fw-semibold small text-uppercase">
                        <i class="bi bi-wallet2 me-1"></i>Pendapatan Bersih Tenan ({{ $persenTenan }}%)
                    </div>
                    <div class="fs-3 fw-bold text-success mt-1">Rp {{ number_format($totalBersihTenan, 0, ',', '.') }}</div>
                    <div class="small text-success-emphasis opacity-75">{{ $persenTenan }}% &times; Rp {{ number_format($totalKotor, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="p-3 rounded bg-primary-subtle border border-primary-subtle h-100">
                    <div class="text-primary-emphasis fw-semibold small text-uppercase">
                        <i class="bi bi-bank me-1"></i>Bagian Admin ({{ $persenAdmin }}%)
                    </div>
                    <div class="fs-3 fw-bold text-primary mt-1">Rp {{ number_format($totalBagianAdmin, 0, ',', '.') }}</div>
                    <div class="small text-primary-emphasis opacity-75">{{ $persenAdmin }}% &times; Rp {{ number_format($totalKotor, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Rekap Bagi Hasil per Tenan</span>
        <span class="badge border bg-light text-dark">{{ $rekapPerTenan->count() }} tenan</span>
    </div>
    <div class="card-body p-0">
        @if($rekapPerTenan->isEmpty())
            <p class="text-center text-muted py-4 mb-0">Tidak ada data pengembalian pada periode ini.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Tenan</th>
                            <th class="text-center">Transaksi</th>
                            <th class="text-end">Total Gobog</th>
                            <th class="text-end">Total Kotor</th>
                            <th class="text-end">Bersih Tenan ({{ $persenTenan }}%)</th>
                            <th class="text-end">Bagian Admin ({{ $persenAdmin }}%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapPerTenan as $row)
                        <tr>
                            <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                            <td class="fw-semibold text-dark">{{ $row['tenan_nama'] }}</td>
                            <td class="text-center text-muted small">{{ $row['jumlah_transaksi'] }}x</td>
                            <td class="text-end fw-semibold text-success">+{{ number_format($row['total_gobog'], 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-dark">Rp {{ number_format($row['total_kotor'], 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-success">Rp {{ number_format($row['pendapatan_bersih'], 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-primary">Rp {{ number_format($row['bagian_admin'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold border-top border-dark">
                        <tr>
                            <td colspan="3" class="text-end">Grand Total:</td>
                            <td class="text-end text-success">+{{ number_format($totalGobogKembali, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($totalKotor, 0, ',', '.') }}</td>
                            <td class="text-end text-success">Rp {{ number_format($totalBersihTenan, 0, ',', '.') }}</td>
                            <td class="text-end text-primary">Rp {{ number_format($totalBagianAdmin, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-semibold">
            <span class="badge bg-success me-2">Masuk</span>Detail Riwayat Pengembalian
        </span>
        <span class="badge border bg-light text-dark">{{ $pengembalians->count() }} transaksi</span>
    </div>
    <div class="card-body p-0">
        @if($pengembalians->isEmpty())
            <p class="text-center text-muted py-4 mb-0">Tidak ada data pengembalian.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Tenan</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end">Harga / Gobog</th>
                            <th class="text-end">Kotor</th>
                            <th class="text-end">Bersih ({{ $persenTenan }}%)</th>
                            <th class="text-end">Admin ({{ $persenAdmin }}%)</th>
                            <th>Dicatat Oleh</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengembalians as $item)
                        <tr>
                            <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                            <td class="fw-semibold text-dark">
                                {{ $item->tenan?->nama ?? '-' }}
                                @if($item->keterangan)
                                    <div class="small text-muted fw-normal">{{ $item->keterangan }}</div>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-success">+{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td class="text-end small text-muted">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-dark">Rp {{ number_format($item->total_nominal, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-success">Rp {{ number_format($item->total_nominal * ($persenTenan / 100), 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-primary">Rp {{ number_format($item->total_nominal * ($persenAdmin / 100), 0, ',', '.') }}</td>
                            <td class="small text-muted">{{ $item->user->nama }}</td>
                            <td class="small text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@if(! $selectedTenan)
<div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-semibold">
            <span class="badge bg-danger me-2">Keluar</span>Detail Riwayat Penjualan Admin
        </span>
        <span class="badge border bg-light text-dark">{{ $penjualans->count() }} transaksi</span>
    </div>
    <div class="card-body p-0">
        @if($penjualans->isEmpty())
            <p class="text-center text-muted py-4 mb-0">Tidak ada data penjualan pada periode ini.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Admin</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end">Harga / Gobog</th>
                            <th class="text-end">Total (Rp)</th>
                            <th>Keterangan</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penjualans as $item)
                        <tr>
                            <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                            <td>{{ $item->user->nama }}</td>
                            <td class="text-end fw-bold text-danger">-{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td class="text-end small text-muted">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-dark">Rp {{ number_format($item->total_nominal, 0, ',', '.') }}</td>
                            <td class="small text-muted">{{ $item->keterangan ?? '-' }}</td>
                            <td class="small text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold border-top border-dark">
                        <tr>
                            <td colspan="2" class="text-end">Total Penjualan:</td>
                            <td class="text-end text-danger">-{{ number_format($totalGobogTerjual, 0, ',', '.') }} Gobog</td>
                            <td></td>
                            <td colspan="3">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-transparent border-bottom fw-semibold">
        Posisi Stok Gobog Saat Ini
    </div>
    <div class="card-body">
        <div class="row text-center g-3">
            <div class="col-4">
                <div class="small text-muted">Total Stok</div>
                <div class="fs-5 fw-bold text-dark">{{ number_format($stock->total_stok, 0, ',', '.') }}</div>
            </div>
            <div class="col-4">
                <div class="small text-muted">Tersedia di Kasir</div>
                <div class="fs-5 fw-bold text-dark">{{ number_format($stock->stok_tersedia, 0, ',', '.') }}</div>
            </div>
            <div class="col-4">
                <div class="small text-muted">Sedang Beredar</div>
                <div class="fs-5 fw-bold text-dark">{{ number_format($stock->stok_beredar, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
