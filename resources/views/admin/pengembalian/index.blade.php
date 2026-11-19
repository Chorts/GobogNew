@extends('layouts.app')
@section('title', 'Pengembalian Gobog')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-arrow-return-left me-2 text-success"></i>Pengembalian Gobog (Masuk)</h4>
    <div class="d-flex flex-wrap gap-2">
        <span class="badge border bg-white text-dark fs-6">Harga: Rp {{ number_format($harga, 0, ',', '.') }}</span>
        <span class="badge border bg-white text-dark fs-6">Bagi Hasil: Tenan {{ $persenTenan }}% / Admin {{ $persenAdmin }}%</span>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm mb-3">
            <div class="card-body text-center py-4">
                <div class="text-muted small fw-semibold text-uppercase">Gobog Sedang Beredar Saat Ini</div>
                <div class="display-5 fw-bold text-dark my-1">{{ number_format($stock->stok_beredar, 0, ',', '.') }}</div>
                <div class="small text-muted">
                    Tersedia di Kasir: <strong>{{ number_format($stock->stok_tersedia, 0, ',', '.') }}</strong> &bull;
                    Total: <strong>{{ number_format($stock->total_stok, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                Form Pengembalian dari Tenan
            </div>
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger py-2 small border-0 mb-3">
                        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                @endif

                @if($tenans->isEmpty())
                    <div class="alert alert-warning small border-0 mb-0">
                        Belum ada data tenan. <a href="{{ route('admin.tenan.create') }}" class="alert-link">Tambah tenan terlebih dahulu</a>.
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.pengembalian.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Pilih Tenan</label>
                            <select name="tenan_id" class="form-select @error('tenan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Tenan --</option>
                                @foreach($tenans as $t)
                                    <option value="{{ $t->id }}" {{ old('tenan_id') == $t->id ? 'selected' : '' }}>{{ $t->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Jumlah Gobog Dikembalikan</label>
                            <input type="number" id="inputJumlah" name="jumlah" class="form-control form-control-lg fw-bold text-center"
                                   value="{{ old('jumlah') }}" min="1" max="{{ $stock->stok_beredar }}"
                                   placeholder="0" required>
                        </div>

                        <div class="d-flex flex-wrap gap-1 mb-3 justify-content-center">
                            @foreach([1, 5, 10, 20, 50, 100] as $preset)
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="tambahJumlah({{ $preset }})">+{{ $preset }}</button>
                            @endforeach
                            @if($stock->stok_beredar > 0)
                                <button type="button" class="btn btn-outline-dark btn-sm" onclick="setJumlah({{ $stock->stok_beredar }})">Semua</button>
                            @endif
                            <button type="button" class="btn btn-outline-dark btn-sm" onclick="setJumlah(0)">Reset</button>
                        </div>

                        <div class="bg-light rounded p-3 mb-3 border">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Total Kotor</span>
                                <span class="fw-bold text-dark" id="previewKotor">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Pendapatan Bersih Tenan ({{ $persenTenan }}%)</span>
                                <span class="fw-bold text-success" id="previewBersih">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Bagian Admin ({{ $persenAdmin }}%)</span>
                                <span class="fw-semibold text-primary" id="previewAdmin">Rp 0</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Sisa Beredar Setelah Retur</span>
                                <span class="fw-semibold small text-dark" id="previewSisa">{{ number_format($stock->stok_beredar, 0, ',', '.') }} Gobog</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Keterangan <span class="text-muted fw-normal">(opsional)</span></label>
                            <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}" placeholder="Contoh: Setoran tutup pasar">
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-semibold py-2" {{ $stock->stok_beredar <= 0 ? 'disabled' : '' }}>
                            <i class="bi bi-check-circle me-1"></i>Catat Pengembalian (Masuk)
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span class="fw-semibold">Pengembalian Hari Ini</span>
                <span class="badge bg-success">
                    +{{ number_format($ringkasanHariIni->total_jumlah, 0, ',', '.') }} Gobog &bull;
                    Kotor Rp {{ number_format($ringkasanHariIni->total_nominal, 0, ',', '.') }}
                </span>
            </div>
            <div class="card-body p-0">
                @if($riwayatHariIni->isEmpty())
                    <div class="text-center py-5 text-muted small">Belum ada pengembalian Gobog hari ini.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 datatable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 45px;">No</th>
                                    <th>Tenan</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-end">Total Kotor</th>
                                    <th class="text-end">Bersih ({{ $persenTenan }}%)</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatHariIni as $item)
                                <tr>
                                    <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $item->tenan?->nama ?? '-' }}</div>
                                        <div class="small text-muted">
                                            {{ $item->user->nama }}
                                            @if($item->keterangan) &bull; {{ $item->keterangan }} @endif
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-success">+{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                    <td class="text-end small text-muted">Rp {{ number_format($item->total_nominal, 0, ',', '.') }}</td>
                                    <td class="text-end fw-semibold text-success">
                                        Rp {{ number_format($item->total_nominal * ($persenTenan / 100), 0, ',', '.') }}
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
const stokBeredar = {{ $stock->stok_beredar }};
const persenTenan = {{ $persenTenan }};
const persenAdmin = {{ $persenAdmin }};

const inputJumlah = document.getElementById('inputJumlah');
const previewKotor = document.getElementById('previewKotor');
const previewBersih = document.getElementById('previewBersih');
const previewAdmin = document.getElementById('previewAdmin');
const previewSisa = document.getElementById('previewSisa');

function updatePreview() {
    if (!inputJumlah) return;
    const val = parseInt(inputJumlah.value, 10) || 0;
    const kotor = val * hargaGobog;
    const bersih = kotor * (persenTenan / 100);
    const admin = kotor * (persenAdmin / 100);
    const sisa = stokBeredar - val;

    previewKotor.textContent = 'Rp ' + kotor.toLocaleString('id-ID');
    previewBersih.textContent = 'Rp ' + bersih.toLocaleString('id-ID');
    previewAdmin.textContent = 'Rp ' + admin.toLocaleString('id-ID');

    if (sisa < 0) {
        previewSisa.textContent = 'Melebihi Gobog beredar!';
        previewSisa.className = 'fw-bold small text-danger';
    } else {
        previewSisa.textContent = sisa.toLocaleString('id-ID') + ' Gobog';
        previewSisa.className = 'fw-semibold small text-dark';
    }
}

function tambahJumlah(n) {
    if (!inputJumlah) return;
    const cur = parseInt(inputJumlah.value, 10) || 0;
    inputJumlah.value = Math.min(stokBeredar, cur + n);
    updatePreview();
}

function setJumlah(n) {
    if (!inputJumlah) return;
    inputJumlah.value = n > 0 ? n : '';
    updatePreview();
}

if (inputJumlah) {
    inputJumlah.addEventListener('input', updatePreview);
    updatePreview();
}
</script>
@endpush
@endsection
