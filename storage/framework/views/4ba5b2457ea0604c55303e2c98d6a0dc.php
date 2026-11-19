<?php $__env->startSection('title', 'Pengembalian Gobog'); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-arrow-return-left me-2 text-success"></i>Pengembalian Gobog (Masuk)</h4>
    <div class="d-flex flex-wrap gap-2">
        <span class="badge border bg-white text-dark fs-6">Harga: Rp <?php echo e(number_format($harga, 0, ',', '.')); ?></span>
        <span class="badge border bg-white text-dark fs-6">Bagi Hasil: Tenan <?php echo e($persenTenan); ?>% / Admin <?php echo e($persenAdmin); ?>%</span>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm mb-3">
            <div class="card-body text-center py-4">
                <div class="text-muted small fw-semibold text-uppercase">Gobog Sedang Beredar Saat Ini</div>
                <div class="display-5 fw-bold text-dark my-1"><?php echo e(number_format($stock->stok_beredar, 0, ',', '.')); ?></div>
                <div class="small text-muted">
                    Tersedia di Kasir: <strong><?php echo e(number_format($stock->stok_tersedia, 0, ',', '.')); ?></strong> &bull;
                    Total: <strong><?php echo e(number_format($stock->total_stok, 0, ',', '.')); ?></strong>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                Form Pengembalian dari Tenan
            </div>
            <div class="card-body p-4">
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger py-2 small border-0 mb-3">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <?php if($tenans->isEmpty()): ?>
                    <div class="alert alert-warning small border-0 mb-0">
                        Belum ada data tenan. <a href="<?php echo e(route('admin.tenan.create')); ?>" class="alert-link">Tambah tenan terlebih dahulu</a>.
                    </div>
                <?php else: ?>
                    <form method="POST" action="<?php echo e(route('admin.pengembalian.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Pilih Tenan</label>
                            <select name="tenan_id" class="form-select <?php $__errorArgs = ['tenan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Pilih Tenan --</option>
                                <?php $__currentLoopData = $tenans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($t->id); ?>" <?php echo e(old('tenan_id') == $t->id ? 'selected' : ''); ?>><?php echo e($t->nama); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Jumlah Gobog Dikembalikan</label>
                            <input type="number" id="inputJumlah" name="jumlah" class="form-control form-control-lg fw-bold text-center"
                                   value="<?php echo e(old('jumlah')); ?>" min="1" max="<?php echo e($stock->stok_beredar); ?>"
                                   placeholder="0" required>
                        </div>

                        <div class="d-flex flex-wrap gap-1 mb-3 justify-content-center">
                            <?php $__currentLoopData = [1, 5, 10, 20, 50, 100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $preset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="tambahJumlah(<?php echo e($preset); ?>)">+<?php echo e($preset); ?></button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($stock->stok_beredar > 0): ?>
                                <button type="button" class="btn btn-outline-dark btn-sm" onclick="setJumlah(<?php echo e($stock->stok_beredar); ?>)">Semua</button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-outline-dark btn-sm" onclick="setJumlah(0)">Reset</button>
                        </div>

                        <div class="bg-light rounded p-3 mb-3 border">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Total Kotor</span>
                                <span class="fw-bold text-dark" id="previewKotor">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Pendapatan Bersih Tenan (<?php echo e($persenTenan); ?>%)</span>
                                <span class="fw-bold text-success" id="previewBersih">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Bagian Admin (<?php echo e($persenAdmin); ?>%)</span>
                                <span class="fw-semibold text-primary" id="previewAdmin">Rp 0</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Sisa Beredar Setelah Retur</span>
                                <span class="fw-semibold small text-dark" id="previewSisa"><?php echo e(number_format($stock->stok_beredar, 0, ',', '.')); ?> Gobog</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Keterangan <span class="text-muted fw-normal">(opsional)</span></label>
                            <input type="text" name="keterangan" class="form-control" value="<?php echo e(old('keterangan')); ?>" placeholder="Contoh: Setoran tutup pasar">
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-semibold py-2" <?php echo e($stock->stok_beredar <= 0 ? 'disabled' : ''); ?>>
                            <i class="bi bi-check-circle me-1"></i>Catat Pengembalian (Masuk)
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span class="fw-semibold">Pengembalian Hari Ini</span>
                <span class="badge bg-success">
                    +<?php echo e(number_format($ringkasanHariIni->total_jumlah, 0, ',', '.')); ?> Gobog &bull;
                    Kotor Rp <?php echo e(number_format($ringkasanHariIni->total_nominal, 0, ',', '.')); ?>

                </span>
            </div>
            <div class="card-body p-0">
                <?php if($riwayatHariIni->isEmpty()): ?>
                    <div class="text-center py-5 text-muted small">Belum ada pengembalian Gobog hari ini.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 datatable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 45px;">No</th>
                                    <th>Tenan</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-end">Total Kotor</th>
                                    <th class="text-end">Bersih (<?php echo e($persenTenan); ?>%)</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $riwayatHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center text-muted small"><?php echo e($loop->iteration); ?></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?php echo e($item->tenan?->nama ?? '-'); ?></div>
                                        <div class="small text-muted">
                                            <?php echo e($item->user->nama); ?>

                                            <?php if($item->keterangan): ?> &bull; <?php echo e($item->keterangan); ?> <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-success">+<?php echo e(number_format($item->jumlah, 0, ',', '.')); ?></td>
                                    <td class="text-end small text-muted">Rp <?php echo e(number_format($item->total_nominal, 0, ',', '.')); ?></td>
                                    <td class="text-end fw-semibold text-success">
                                        Rp <?php echo e(number_format($item->total_nominal * ($persenTenan / 100), 0, ',', '.')); ?>

                                    </td>
                                    <td class="small text-muted"><?php echo e($item->created_at->format('H:i')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
const hargaGobog = <?php echo e($harga); ?>;
const stokBeredar = <?php echo e($stock->stok_beredar); ?>;
const persenTenan = <?php echo e($persenTenan); ?>;
const persenAdmin = <?php echo e($persenAdmin); ?>;

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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CENT\Downloads\Program\Gobog\Sistem Gobog\Sistem Gobog New\resources\views/admin/pengembalian/index.blade.php ENDPATH**/ ?>