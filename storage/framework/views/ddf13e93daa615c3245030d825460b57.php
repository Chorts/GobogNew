<?php $__env->startSection('title', 'Dashboard Admin'); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h4 class="fw-bold mb-0">Dashboard Admin</h4>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.penjualan.index')); ?>" class="btn btn-danger btn-sm">
            <i class="bi bi-bag-check me-1"></i>Penjualan (Keluar)
        </a>
        <a href="<?php echo e(route('admin.pengembalian.index')); ?>" class="btn btn-success btn-sm">
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
                        <div class="fs-3 fw-bold text-dark mt-1"><?php echo e(number_format($stock->total_stok, 0, ',', '.')); ?></div>
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
                        <div class="fs-3 fw-bold text-dark mt-1"><?php echo e(number_format($stock->stok_tersedia, 0, ',', '.')); ?></div>
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
                        <div class="fs-3 fw-bold text-dark mt-1"><?php echo e(number_format($stock->stok_beredar, 0, ',', '.')); ?></div>
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
                        <div class="fs-4 fw-bold text-dark mt-1">Rp <?php echo e(number_format($harga, 0, ',', '.')); ?></div>
                        <div class="text-muted small">Admin <?php echo e($persenAdmin); ?>% &bull; Tenan <?php echo e($persenTenan); ?>%</div>
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
                    <div class="fs-4 fw-bold text-danger">-<?php echo e(number_format($penjualanHariIni->total_jumlah, 0, ',', '.')); ?> Gobog</div>
                    <div class="small text-muted">Rp <?php echo e(number_format($penjualanHariIni->total_nominal, 0, ',', '.')); ?></div>
                </div>
                <a href="<?php echo e(route('admin.penjualan.index')); ?>" class="btn btn-outline-danger btn-sm">Catat Penjualan</a>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Pengembalian Hari Ini (Masuk)</div>
                    <div class="fs-4 fw-bold text-success">+<?php echo e(number_format($pengembalianHariIni->total_jumlah, 0, ',', '.')); ?> Gobog</div>
                    <div class="small text-muted">Kotor: Rp <?php echo e(number_format($pengembalianHariIni->total_nominal, 0, ',', '.')); ?></div>
                </div>
                <a href="<?php echo e(route('admin.pengembalian.index')); ?>" class="btn btn-outline-success btn-sm">Catat Pengembalian</a>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-semibold">10 Transaksi Terakhir</span>
        <a href="<?php echo e(route('admin.laporan.index')); ?>" class="btn btn-sm btn-outline-dark">Lihat Laporan</a>
    </div>
    <div class="card-body p-0">
        <?php if($transaksiTerakhir->isEmpty()): ?>
            <div class="text-center py-4 text-muted small">Belum ada transaksi.</div>
        <?php else: ?>
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
                        <?php $__currentLoopData = $transaksiTerakhir; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="text-center text-muted small"><?php echo e($loop->iteration); ?></td>
                            <td>
                                <?php if($tx->tipe === 'penjualan'): ?>
                                    <span class="badge bg-danger">Keluar</span>
                                <?php elseif($tx->tipe === 'kurangi_stok'): ?>
                                    <span class="badge bg-danger">- Stok</span>
                                <?php elseif($tx->tipe === 'pengembalian'): ?>
                                    <span class="badge bg-success">Masuk</span>
                                <?php else: ?>
                                    <span class="badge bg-success">+ Stok</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($tx->tenan): ?>
                                    <span class="fw-semibold text-dark"><?php echo e($tx->tenan->nama); ?></span>
                                    <?php if($tx->keterangan): ?>
                                        <div class="small text-muted"><?php echo e($tx->keterangan); ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted small"><?php echo e($tx->keterangan ?? '-'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-semibold <?php echo e($tx->tipe === 'penjualan' || $tx->tipe === 'kurangi_stok' ? 'text-danger' : 'text-success'); ?>">
                                <?php echo e($tx->tipe === 'penjualan' || $tx->tipe === 'kurangi_stok' ? '-' : '+'); ?><?php echo e(number_format($tx->jumlah, 0, ',', '.')); ?>

                            </td>
                            <td class="text-end small text-muted">Rp <?php echo e(number_format($tx->harga_satuan, 0, ',', '.')); ?></td>
                            <td class="text-end fw-semibold text-dark">Rp <?php echo e(number_format($tx->total_nominal, 0, ',', '.')); ?></td>
                            <td class="small text-muted"><?php echo e($tx->user->nama); ?></td>
                            <td class="small text-muted"><?php echo e($tx->created_at->format('d/m/Y H:i')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CENT\Downloads\Program\Gobog\Sistem Gobog\Sistem Gobog New\resources\views/admin/dashboard/index.blade.php ENDPATH**/ ?>