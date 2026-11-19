@extends('layouts.app')
@section('title', 'Penjualan Gobog')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-bag-check me-2 text-danger"></i>Penjualan Gobog (Keluar)</h4>
    <span class="badge border bg-white text-dark fs-6">Harga: Rp {{ number_format($harga, 0, ',', '.') }} / Gobog</span>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm mb-3">
            <div class="card-body text-center py-4">
                <div class="text-muted small fw-semibold text-uppercase">Stok Gobog Tersedia Saat Ini</div>
                <div class="display-5 fw-bold text-dark my-1">{{ number_format($stock->stok_tersedia, 0, ',', '.') }}</div>
                <div class="small text-muted">
                    Total Keseluruhan: <strong>{{ number_format($stock->total_stok, 0, ',', '.') }}</strong> &bull;
                    Sedang Beredar: <strong>{{ number_format($stock->stok_beredar, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                Form Penjualan ke Pengunjung
            </div>
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger py-2 small border-0 mb-3">
                        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.penjualan.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jumlah Gobog Dijual</label>
                        <input type="number" id="inputJumlah" name="jumlah" class="form-control form-control-lg fw-bold text-center"
                               value="{{ old('jumlah') }}" min="1" max="{{ $stock->stok_tersedia }}"
                               placeholder="0" required autofocus>
                    </div>

                    <div class="d-flex flex-wrap gap-1 mb-3 justify-content-center">
                        @foreach([1, 5, 10, 20, 50, 100] as $preset)
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="tambahJumlah({{ $preset }})">+{{ $preset }}</button>
                        @endforeach
                        <button type="button" class="btn btn-outline-dark btn-sm" onclick="setJumlah(0)">Reset</button>
                    </div>

                    <div class="bg-light rounded p-3 mb-3 border">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Total Pembayaran</span>
                            <span class="fw-bold text-danger fs-5" id="previewTotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Sisa Stok Tersedia</span>
                            <span class="fw-semibold small" id="previewSisa">{{ number_format($stock->stok_tersedia, 0, ',', '.') }} Gobog</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}" placeholder="Contoh: Rombongan wisata">
                    </div>

                    <button type="submit" id="btnSubmit" class="btn btn-danger w-100 fw-semibold py-2" {{ $stock->stok_tersedia <= 0 ? 'disabled' : '' }}>
                        <i class="bi bi-check-circle me-1"></i>Catat Penjualan (Keluar)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span class="fw-semibold">Penjualan Hari Ini</span>
                <span class="badge bg-danger">
                    -{{ number_format($ringkasanHariIni->total_jumlah, 0, ',', '.') }} Gobog &bull;
                    Rp {{ number_format($ringkasanHariIni->total_nominal, 0, ',', '.') }}
                </span>
            </div>
            <div class="card-body p-0">
                @if($riwayatHariIni->isEmpty())
                    <div class="text-center py-5 text-muted small">Belum ada transaksi penjualan hari ini.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 datatable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 45px;">No</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Total (Rp)</th>
                                    <th>Admin</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatHariIni as $item)
                                <tr>
                                    <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                                    <td class="text-end fw-bold text-danger">-{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                    <td class="text-end small text-muted">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-end fw-semibold text-dark">Rp {{ number_format($item->total_nominal, 0, ',', '.') }}</td>
                                    <td class="small">
                                        {{ $item->user->nama }}
                                        @if($item->keterangan)
                                            <div class="text-muted" style="font-size:.75rem">{{ $item->keterangan }}</div>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $item->created_at->format('H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const hargaGobog = {{ $harga }};
const stokTersedia = {{ $stock->stok_tersedia }};
const inputJumlah = document.getElementById('inputJumlah');
const previewTotal = document.getElementById('previewTotal');
const previewSisa = document.getElementById('previewSisa');

function updatePreview() {
    const val = parseInt(inputJumlah.value, 10) || 0;
    const total = val * hargaGobog;
    const sisa = stokTersedia - val;

    previewTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
    if (sisa < 0) {
        previewSisa.textContent = 'Melebihi stok tersedia!';
        previewSisa.className = 'fw-bold small text-danger';
    } else {
        previewSisa.textContent = sisa.toLocaleString('id-ID') + ' Gobog';
        previewSisa.className = 'fw-semibold small text-dark';
    }
}

function tambahJumlah(n) {
    const cur = parseInt(inputJumlah.value, 10) || 0;
    inputJumlah.value = Math.min(stokTersedia, cur + n);
    updatePreview();
}

function setJumlah(n) {
    inputJumlah.value = n > 0 ? n : '';
    updatePreview();
}

inputJumlah.addEventListener('input', updatePreview);
updatePreview();
</script>
@endpush
@endsection
