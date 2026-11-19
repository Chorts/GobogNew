@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h4 class="fw-bold mb-0">Dashboard Admin</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.penjualan.index') }}" class="btn btn-danger btn-sm">
            <i class="bi bi-bag-check me-1"></i>Penjualan (Keluar)
        </a>
        <a href="{{ route('admin.pengembalian.index') }}" class="btn btn-success btn-sm">
            <i class="bi bi-arrow-return-left me-1"></i>Pengembalian (Masuk)
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold">Total Stok Gobog</div>
                        <div class="fs-3 fw-bold text-dark mt-1">{{ number_format($stock->total_stok, 0, ',', '.') }}</div>
                        <div class="text-muted small">Total keseluruhan</div>
                    </div>
                    <span class="text-secondary fs-4"><i class="bi bi-coin"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold">Tersedia di Kasir</div>
                        <div class="fs-3 fw-bold text-dark mt-1">{{ number_format($stock->stok_tersedia, 0, ',', '.') }}</div>
                        <div class="text-muted small">Siap dijual ke pengunjung</div>
                    </div>
                    <span class="text-secondary fs-4"><i class="bi bi-box-seam"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold">Sedang Beredar</div>
                        <div class="fs-3 fw-bold text-dark mt-1">{{ number_format($stock->stok_beredar, 0, ',', '.') }}</div>
                        <div class="text-muted small">Di pengunjung / tenan</div>
                    </div>
                    <span class="text-secondary fs-4"><i class="bi bi-arrow-left-right"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-semibold">Harga &amp; Bagi Hasil</div>
                        <div class="fs-4 fw-bold text-dark mt-1">Rp {{ number_format($harga, 0, ',', '.') }}</div>
                        <div class="text-muted small">Admin {{ $persenAdmin }}% &bull; Tenan {{ $persenTenan }}%</div>
                    </div>
                    <span class="text-secondary fs-4"><i class="bi bi-percent"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Penjualan Hari Ini (Keluar)</div>
                    <div class="fs-4 fw-bold text-danger">-{{ number_format($penjualanHariIni->total_jumlah, 0, ',', '.') }} Gobog</div>
                    <div class="small text-muted">Rp {{ number_format($penjualanHariIni->total_nominal, 0, ',', '.') }}</div>
                </div>
                <a href="{{ route('admin.penjualan.index') }}" class="btn btn-outline-danger btn-sm">Catat Penjualan</a>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Pengembalian Hari Ini (Masuk)</div>
                    <div class="fs-4 fw-bold text-success">+{{ number_format($pengembalianHariIni->total_jumlah, 0, ',', '.') }} Gobog</div>
                    <div class="small text-muted">Kotor: Rp {{ number_format($pengembalianHariIni->total_nominal, 0, ',', '.') }}</div>
                </div>
                <a href="{{ route('admin.pengembalian.index') }}" class="btn btn-outline-success btn-sm">Catat Pengembalian</a>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-semibold">10 Transaksi Terakhir</span>
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm btn-outline-dark">Lihat Laporan</a>
    </div>
    <div class="card-body p-0">
        @if($transaksiTerakhir->isEmpty())
            <div class="text-center py-4 text-muted small">Belum ada transaksi.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Tipe</th>
                            <th>Tenan / Keterangan</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Total (Rp)</th>
                            <th>Admin</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksiTerakhir as $tx)
                        <tr>
                            <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                @if($tx->tipe === 'penjualan')
                                    <span class="badge bg-danger">Keluar</span>
                                @elseif($tx->tipe === 'kurangi_stok')
                                    <span class="badge bg-danger">- Stok</span>
                                @elseif($tx->tipe === 'pengembalian')
                                    <span class="badge bg-success">Masuk</span>
                                @else
                                    <span class="badge bg-success">+ Stok</span>
                                @endif
                            </td>
                            <td>
                                @if($tx->tenan)
                                    <span class="fw-semibold text-dark">{{ $tx->tenan->nama }}</span>
                                    @if($tx->keterangan)
                                        <div class="small text-muted">{{ $tx->keterangan }}</div>
                                    @endif
                                @else
                                    <span class="text-muted small">{{ $tx->keterangan ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="text-end fw-semibold {{ $tx->tipe === 'penjualan' || $tx->tipe === 'kurangi_stok' ? 'text-danger' : 'text-success' }}">
                                {{ $tx->tipe === 'penjualan' || $tx->tipe === 'kurangi_stok' ? '-' : '+' }}{{ number_format($tx->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="text-end small text-muted">Rp {{ number_format($tx->harga_satuan, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-dark">Rp {{ number_format($tx->total_nominal, 0, ',', '.') }}</td>
                            <td class="small text-muted">{{ $tx->user->nama }}</td>
                            <td class="small text-muted">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
