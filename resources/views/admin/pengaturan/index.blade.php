@extends('layouts.app')
@section('title', 'Pengaturan Sistem')
@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-gear me-2"></i>Pengaturan Sistem</h4>

@if($errors->any())
    <div class="alert alert-danger py-2 small">
        @foreach($errors->all() as $e)<div><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</div>@endforeach
    </div>
@endif

<div class="row g-4 mb-4">
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                <i class="bi bi-cash-coin me-2"></i>Harga Global Gobog
            </div>
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="fs-2 fw-bold text-dark">Rp {{ number_format($harga, 0, ',', '.') }}</div>
                    <div class="text-muted small">Harga per Gobog saat ini</div>
                </div>
                <form method="POST" action="{{ route('admin.pengaturan.harga') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Harga Baru (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga_gobog" class="form-control"
                                   value="{{ old('harga_gobog', $harga) }}" min="0" step="100" required>
                        </div>
                        <div class="form-text">Transaksi lama tetap menyimpan harga pada tanggal transaksi tersebut.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan Perubahan <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="text" name="keterangan" class="form-control"
                               placeholder="Contoh: Penyesuaian harga acara" value="{{ old('keterangan') }}">
                    </div>
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">
                        <i class="bi bi-save me-1"></i>Simpan Harga Gobog
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                <i class="bi bi-percent me-2"></i>Persentase Bagi Hasil (Admin &amp; Tenan)
            </div>
            <div class="card-body p-4">
                <div class="row text-center mb-4">
                    <div class="col-6 border-end">
                        <div class="fs-2 fw-bold text-primary">{{ $persenAdmin }}%</div>
                        <div class="text-muted small">Bagian Admin</div>
                    </div>
                    <div class="col-6">
                        <div class="fs-2 fw-bold text-success">{{ $persenTenan }}%</div>
                        <div class="text-muted small">Pendapatan Tenan</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.pengaturan.bagi-hasil') }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Persentase Admin (%)</label>
                            <div class="input-group">
                                <input type="number" id="persenAdmin" name="persen_admin" class="form-control"
                                       value="{{ old('persen_admin', $persenAdmin) }}" min="0" max="100" step="0.5" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Persentase Tenan (%)</label>
                            <div class="input-group">
                                <input type="number" id="persenTenan" name="persen_tenan" class="form-control"
                                       value="{{ old('persen_tenan', $persenTenan) }}" min="0" max="100" step="0.5" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-text mb-3">
                        Total harus 100%. Perubahan persentase langsung berlaku dinamis pada perhitungan laporan.
                    </div>
                    <button type="submit" class="btn btn-dark w-100 fw-semibold">
                        <i class="bi bi-save me-1"></i>Simpan Persentase Bagi Hasil
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-bottom fw-semibold">
        <i class="bi bi-clock-history me-2"></i>Riwayat Perubahan Harga Gobog
    </div>
    <div class="card-body p-0">
        @if($riwayat->isEmpty())
            <div class="text-center py-4 text-muted small">Belum ada riwayat perubahan harga.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Harga</th>
                            <th>Keterangan</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayat as $r)
                        <tr>
                            <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">Rp {{ number_format($r->harga, 0, ',', '.') }}</td>
                            <td class="text-muted small">{{ $r->keterangan ?? '-' }}</td>
                            <td class="small text-muted">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
const inputAdmin = document.getElementById('persenAdmin');
const inputTenan = document.getElementById('persenTenan');

inputAdmin.addEventListener('input', function () {
    const val = parseFloat(this.value);
    if (!isNaN(val) && val >= 0 && val <= 100) {
        inputTenan.value = Number((100 - val).toFixed(2));
    }
});

inputTenan.addEventListener('input', function () {
    const val = parseFloat(this.value);
    if (!isNaN(val) && val >= 0 && val <= 100) {
        inputAdmin.value = Number((100 - val).toFixed(2));
    }
});
</script>
@endpush
@endsection
