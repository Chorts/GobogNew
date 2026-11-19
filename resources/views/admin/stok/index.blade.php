@extends('layouts.app')
@section('title', 'Stok Gobog')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Stok Gobog</h4>
    <span class="badge border bg-white text-dark fs-6">Harga Aktif: Rp {{ number_format($harga, 0, ',', '.') }}</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card shadow-sm h-100 text-center">
            <div class="card-body py-4">
                <div class="text-muted small fw-semibold">Total Keseluruhan Gobog</div>
                <div class="display-6 fw-bold text-dark my-1">{{ number_format($stock->total_stok, 0, ',', '.') }}</div>
                <div class="small text-muted">Seluruh Gobog milik pasar</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card shadow-sm h-100 text-center">
            <div class="card-body py-4">
                <div class="text-muted small fw-semibold">Tersedia di Kasir Admin</div>
                <div class="display-6 fw-bold text-dark my-1">{{ number_format($stock->stok_tersedia, 0, ',', '.') }}</div>
                <div class="small text-muted">Siap dijual ke pengunjung</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card shadow-sm h-100 text-center">
            <div class="card-body py-4">
                <div class="text-muted small fw-semibold">Sedang Beredar</div>
                <div class="display-6 fw-bold text-dark my-1">{{ number_format($stock->stok_beredar, 0, ',', '.') }}</div>
                <div class="small text-muted">Belum kembali dari tenan</div>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger py-2 small border-0 mb-4">
        @foreach($errors->all() as $e)<div><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</div>@endforeach
    </div>
@endif

<div class="row g-4 mb-4">
    <div class="col-12 col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                <i class="bi bi-plus-circle me-2 text-success"></i>Tambah Stok Gobog (Masuk)
            </div>
            <div class="card-body p-4">
                <p class="small text-muted">Menambah total keseluruhan stok Gobog sekaligus menambah stok tersedia di kasir admin.</p>
                <form method="POST" action="{{ route('admin.stok.tambah') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jumlah Gobog Baru</label>
                        <input type="number" name="jumlah" class="form-control" min="1" max="1000000" placeholder="Contoh: 1000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Penambahan stok baru">
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i>Tambah ke Total Stok (Masuk)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                <i class="bi bi-dash-circle me-2 text-danger"></i>Kurangi Stok Gobog (Keluar)
            </div>
            <div class="card-body p-4">
                <p class="small text-muted">Mengurangi total keseluruhan dari stok yang tersedia di kasir (misal untuk Gobog rusak atau hilang).</p>
                <form method="POST" action="{{ route('admin.stok.kurangi') }}" onsubmit="return confirm('Yakin mengurangi stok keseluruhan Gobog?')">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jumlah Pengurangan (maks. {{ number_format($stock->stok_tersedia, 0, ',', '.') }})</label>
                        <input type="number" name="jumlah" class="form-control" min="1" max="{{ $stock->stok_tersedia }}" placeholder="Contoh: 10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Alasan Pengurangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Gobog rusak / hilang" required>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 fw-semibold" {{ $stock->stok_tersedia <= 0 ? 'disabled' : '' }}>
                        <i class="bi bi-dash-lg me-1"></i>Kurangi Stok (Keluar)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-transparent border-bottom fw-semibold">
        Riwayat Perubahan Stok
    </div>
    <div class="card-body p-0">
        @if($riwayat->isEmpty())
            <div class="text-center py-4 text-muted small">Belum ada riwayat penambahan atau pengurangan stok.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Tipe</th>
                            <th class="text-end">Jumlah</th>
                            <th>Keterangan</th>
                            <th>Admin</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayat as $item)
                        <tr>
                            <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                @if($item->tipe === 'tambah_stok')
                                    <span class="badge bg-success">+ Stok</span>
                                @else
                                    <span class="badge bg-danger">- Stok</span>
                                @endif
                            </td>
                            <td class="text-end fw-semibold {{ $item->tipe === 'tambah_stok' ? 'text-success' : 'text-danger' }}">
                                {{ $item->tipe === 'tambah_stok' ? '+' : '-' }}{{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="small">{{ $item->keterangan ?? '-' }}</td>
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
@endsection
