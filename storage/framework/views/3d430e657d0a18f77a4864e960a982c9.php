<?php $__env->startSection('title', 'Penjualan Gobog'); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-bag-check me-2 text-danger"></i>Penjualan Gobog (Keluar)</h4>
    <span class="badge border bg-white text-dark fs-6">Harga: Rp <?php echo e(number_format($harga, 0, ',', '.')); ?> / Gobog</span>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm mb-3">
            <div class="card-body text-center py-4">
                <div class="text-muted small fw-semibold text-uppercase">Stok Gobog Tersedia Saat Ini</div>
                <div class="display-5 fw-bold text-dark my-1"><?php echo e(number_format($stock->stok_tersedia, 0, ',', '.')); ?></div>
                <div class="small text-muted">
                    Total Keseluruhan: <strong><?php echo e(number_format($stock->total_stok, 0, ',', '.')); ?></strong> &bull;
                    Sedang Beredar: <strong><?php echo e(number_format($stock->stok_beredar, 0, ',', '.')); ?></strong>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                Form Penjualan ke Pengunjung
            </div>
            <div class="card-body p-4">
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger py-2 small border-0 mb-3">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('admin.penjualan.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jumlah Gobog Dijual</label>
                        <input type="number" id="inputJumlah" name="jumlah" class="form-control form-control-lg fw-bold text-center"
                               value="<?php echo e(old('jumlah')); ?>" min="1" max="<?php echo e($stock->stok_tersedia); ?>"
                               placeholder="0" required autofocus>
                    </div>

                    <div class="d-flex flex-wrap gap-1 mb-3 justify-content-center">
                        <?php $__currentLoopData = [1, 5, 10, 20, 50, 100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $preset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="tambahJumlah(<?php echo e($preset); ?>)">+<?php echo e($preset); ?></button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="btn btn-outline-dark btn-sm" onclick="setJumlah(0)">Reset</button>
                    </div>

                    <div class="bg-light rounded p-3 mb-3 border">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Total Pembayaran</span>
                            <span class="fw-bold text-danger fs-5" id="previewTotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Sisa Stok Tersedia</span>
                            <span class="fw-semibold small" id="previewSisa"><?php echo e(number_format($stock->stok_tersedia, 0, ',', '.')); ?> Gobog</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="text" name="keterangan" class="form-control" value="<?php echo e(old('keterangan')); ?>" placeholder="Contoh: Rombongan wisata">
                    </div>

                    <button type="submit" id="btnSubmit" class="btn btn-danger w-100 fw-semibold py-2" <?php echo e($stock->stok_tersedia <= 0 ? 'disabled' : ''); ?>>
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
                    -<?php echo e(number_format($ringkasanHariIni->total_jumlah, 0, ',', '.')); ?> Gobog &bull;
                    Rp <?php echo e(number_format($ringkasanHariIni->total_nominal, 0, ',', '.')); ?>

                </span>
            </div>
            <div class="card-body p-0">
                <?php if($riwayatHariIni->isEmpty()): ?>
                    <div class="text-center py-5 text-muted small">Belum ada transaksi penjualan hari ini.</div>
                <?php else: ?>
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
                                <?php $__currentLoopData = $riwayatHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center text-muted small"><?php echo e($loop->iteration); ?></td>
                                    <td class="text-end fw-bold text-danger">-<?php echo e(number_format($item->jumlah, 0, ',', '.')); ?></td>
                                    <td class="text-end small text-muted"><?php echo e(number_format($item->harga_satuan, 0, ',', '.')); ?></td>
                                    <td class="text-end fw-semibold text-dark">Rp <?php echo e(number_format($item->total_nominal, 0, ',', '.')); ?></td>
                                    <td class="small">
                                        <?php echo e($item->user->nama); ?>

                                        <?php if($item->keterangan): ?>
                                            <div class="text-muted" style="font-size:.75rem"><?php echo e($item->keterangan); ?></div>
                                        <?php endif; ?>
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
const stokTersedia = <?php echo e($stock->stok_tersedia); ?>;
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CENT\Downloads\Program\Gobog\Sistem Gobog\Sistem Gobog New\resources\views/admin/penjualan/index.blade.php ENDPATH**/ ?>