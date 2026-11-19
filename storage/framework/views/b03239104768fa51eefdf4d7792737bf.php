<?php $__env->startSection('title', 'Laporan'); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h4 class="fw-bold mb-0">Laporan</h4>
    <a href="<?php echo e(route('admin.laporan.export-pdf', request()->query())); ?>" class="btn btn-dark btn-sm">
        <i class="bi bi-file-pdf me-1"></i>Export PDF
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="<?php echo e(route('admin.laporan.index')); ?>" class="row g-2 align-items-end">
            <div class="col-6 col-sm-auto">
                <label class="form-label small text-muted mb-1">Periode</label>
                <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="harian" <?php echo e($filter === 'harian' ? 'selected' : ''); ?>>Harian</option>
                    <option value="mingguan" <?php echo e($filter === 'mingguan' ? 'selected' : ''); ?>>Mingguan</option>
                    <option value="bulanan" <?php echo e($filter === 'bulanan' ? 'selected' : ''); ?>>Bulanan</option>
                    <option value="tahunan" <?php echo e($filter === 'tahunan' ? 'selected' : ''); ?>>Tahunan</option>
                </select>
            </div>

            <?php if(in_array($filter, ['harian', 'mingguan'])): ?>
                <div class="col-6 col-sm-auto">
                    <label class="form-label small text-muted mb-1">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm" value="<?php echo e($tanggal); ?>">
                </div>
            <?php elseif($filter === 'bulanan'): ?>
                <div class="col-6 col-sm-auto">
                    <label class="form-label small text-muted mb-1">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm">
                        <?php for($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo e($m); ?>" <?php echo e($bulan === $m ? 'selected' : ''); ?>>
                                <?php echo e(\Carbon\Carbon::createFromDate(2000, $m, 1)->translatedFormat('F')); ?>

                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-6 col-sm-auto">
                    <label class="form-label small text-muted mb-1">Tahun</label>
                    <input type="number" name="tahun" class="form-control form-control-sm" style="width:90px" value="<?php echo e($tahun); ?>">
                </div>
            <?php else: ?>
                <div class="col-6 col-sm-auto">
                    <label class="form-label small text-muted mb-1">Tahun</label>
                    <input type="number" name="tahun" class="form-control form-control-sm" style="width:90px" value="<?php echo e($tahun); ?>">
                </div>
            <?php endif; ?>

            <div class="col-12 col-sm-auto">
                <label class="form-label small text-muted mb-1">Filter Tenan</label>
                <select name="tenan_id" class="form-select form-select-sm">
                    <option value="">Semua Tenan</option>
                    <?php $__currentLoopData = $tenans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($t->id); ?>" <?php echo e($tenanId === $t->id ? 'selected' : ''); ?>><?php echo e($t->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-dark btn-sm">
                    <i class="bi bi-search me-1"></i>Tampilkan
                </button>
                <a href="<?php echo e(route('admin.laporan.index')); ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm mb-4 border bg-white">
    <div class="card-body py-2 d-flex flex-wrap justify-content-between align-items-center gap-2 small">
        <div>
            <strong>Periode:</strong> <?php echo e($periodeLabel); ?>

            <span class="text-muted mx-2">|</span>
            <strong>Tenan:</strong> <?php echo e($selectedTenan ? $selectedTenan->nama : 'Semua Tenan'); ?>

            <span class="text-muted mx-2">|</span>
            <strong>Bagi Hasil Aktif:</strong> Tenan <span class="badge border bg-white text-dark"><?php echo e($persenTenan); ?>%</span> / Admin <span class="badge border bg-white text-dark"><?php echo e($persenAdmin); ?>%</span>
        </div>
        <a href="<?php echo e(route('admin.pengaturan.index')); ?>" class="text-dark text-decoration-none fw-semibold">
            <i class="bi bi-sliders me-1"></i>Ubah Persentase
        </a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Ringkasan Pendapatan <?php echo e($selectedTenan ? '— '.$selectedTenan->nama : '(Semua Tenan)'); ?></span>
        <span class="badge bg-success">+<?php echo e(number_format($totalGobogKembali, 0, ',', '.')); ?> Gobog Masuk</span>
    </div>
    <div class="card-body">
        <div class="text-center pb-3 mb-3 border-bottom">
            <div class="text-muted small text-uppercase fw-semibold">Total Pendapatan Kotor</div>
            <div class="display-6 fw-bold text-dark">Rp <?php echo e(number_format($totalKotor, 0, ',', '.')); ?></div>
            <div class="small text-muted">Dihitung dari harga Gobog masing-masing pada tanggal transaksi</div>
        </div>
        <div class="row g-3 text-center">
            <div class="col-12 col-md-6">
                <div class="p-3 rounded bg-success-subtle border border-success-subtle h-100">
                    <div class="text-success-emphasis fw-semibold small text-uppercase">
                        <i class="bi bi-wallet2 me-1"></i>Pendapatan Bersih Tenan (<?php echo e($persenTenan); ?>%)
                    </div>
                    <div class="fs-3 fw-bold text-success mt-1">Rp <?php echo e(number_format($totalBersihTenan, 0, ',', '.')); ?></div>
                    <div class="small text-success-emphasis opacity-75"><?php echo e($persenTenan); ?>% &times; Rp <?php echo e(number_format($totalKotor, 0, ',', '.')); ?></div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="p-3 rounded bg-primary-subtle border border-primary-subtle h-100">
                    <div class="text-primary-emphasis fw-semibold small text-uppercase">
                        <i class="bi bi-bank me-1"></i>Bagian Admin (<?php echo e($persenAdmin); ?>%)
                    </div>
                    <div class="fs-3 fw-bold text-primary mt-1">Rp <?php echo e(number_format($totalBagianAdmin, 0, ',', '.')); ?></div>
                    <div class="small text-primary-emphasis opacity-75"><?php echo e($persenAdmin); ?>% &times; Rp <?php echo e(number_format($totalKotor, 0, ',', '.')); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Rekap Bagi Hasil per Tenan</span>
        <span class="badge border bg-light text-dark"><?php echo e($rekapPerTenan->count()); ?> tenan</span>
    </div>
    <div class="card-body p-0">
        <?php if($rekapPerTenan->isEmpty()): ?>
            <p class="text-center text-muted py-4 mb-0">Tidak ada data pengembalian pada periode ini.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Tenan</th>
                            <th class="text-center">Transaksi</th>
                            <th class="text-end">Total Gobog</th>
                            <th class="text-end">Total Kotor</th>
                            <th class="text-end">Bersih Tenan (<?php echo e($persenTenan); ?>%)</th>
                            <th class="text-end">Bagian Admin (<?php echo e($persenAdmin); ?>%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $rekapPerTenan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="text-center text-muted small"><?php echo e($loop->iteration); ?></td>
                            <td class="fw-semibold text-dark"><?php echo e($row['tenan_nama']); ?></td>
                            <td class="text-center text-muted small"><?php echo e($row['jumlah_transaksi']); ?>x</td>
                            <td class="text-end fw-semibold text-success">+<?php echo e(number_format($row['total_gobog'], 0, ',', '.')); ?></td>
                            <td class="text-end fw-semibold text-dark">Rp <?php echo e(number_format($row['total_kotor'], 0, ',', '.')); ?></td>
                            <td class="text-end fw-semibold text-success">Rp <?php echo e(number_format($row['pendapatan_bersih'], 0, ',', '.')); ?></td>
                            <td class="text-end fw-semibold text-primary">Rp <?php echo e(number_format($row['bagian_admin'], 0, ',', '.')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot class="table-light fw-bold border-top border-dark">
                        <tr>
                            <td colspan="3" class="text-end">Grand Total:</td>
                            <td class="text-end text-success">+<?php echo e(number_format($totalGobogKembali, 0, ',', '.')); ?></td>
                            <td class="text-end">Rp <?php echo e(number_format($totalKotor, 0, ',', '.')); ?></td>
                            <td class="text-end text-success">Rp <?php echo e(number_format($totalBersihTenan, 0, ',', '.')); ?></td>
                            <td class="text-end text-primary">Rp <?php echo e(number_format($totalBagianAdmin, 0, ',', '.')); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-semibold">
            <span class="badge bg-success me-2">Masuk</span>Detail Riwayat Pengembalian
        </span>
        <span class="badge border bg-light text-dark"><?php echo e($pengembalians->count()); ?> transaksi</span>
    </div>
    <div class="card-body p-0">
        <?php if($pengembalians->isEmpty()): ?>
            <p class="text-center text-muted py-4 mb-0">Tidak ada data pengembalian.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">No</th>
                            <th>Tenan</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end">Harga / Gobog</th>
                            <th class="text-end">Kotor</th>
                            <th class="text-end">Bersih (<?php echo e($persenTenan); ?>%)</th>
                            <th class="text-end">Admin (<?php echo e($persenAdmin); ?>%)</th>
                            <th>Dicatat Oleh</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $pengembalians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="text-center text-muted small"><?php echo e($loop->iteration); ?></td>
                            <td class="fw-semibold text-dark">
                                <?php echo e($item->tenan?->nama ?? '-'); ?>

                                <?php if($item->keterangan): ?>
                                    <div class="small text-muted fw-normal"><?php echo e($item->keterangan); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold text-success">+<?php echo e(number_format($item->jumlah, 0, ',', '.')); ?></td>
                            <td class="text-end small text-muted">Rp <?php echo e(number_format($item->harga_satuan, 0, ',', '.')); ?></td>
                            <td class="text-end fw-semibold text-dark">Rp <?php echo e(number_format($item->total_nominal, 0, ',', '.')); ?></td>
                            <td class="text-end fw-semibold text-success">Rp <?php echo e(number_format($item->total_nominal * ($persenTenan / 100), 0, ',', '.')); ?></td>
                            <td class="text-end fw-semibold text-primary">Rp <?php echo e(number_format($item->total_nominal * ($persenAdmin / 100), 0, ',', '.')); ?></td>
                            <td class="small text-muted"><?php echo e($item->user->nama); ?></td>
                            <td class="small text-muted"><?php echo e($item->created_at->format('d/m/Y H:i')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if(! $selectedTenan): ?>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-semibold">
            <span class="badge bg-danger me-2">Keluar</span>Detail Riwayat Penjualan Admin
        </span>
        <span class="badge border bg-light text-dark"><?php echo e($penjualans->count()); ?> transaksi</span>
    </div>
    <div class="card-body p-0">
        <?php if($penjualans->isEmpty()): ?>
            <p class="text-center text-muted py-4 mb-0">Tidak ada data penjualan pada periode ini.</p>
        <?php else: ?>
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
                        <?php $__currentLoopData = $penjualans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="text-center text-muted small"><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($item->user->nama); ?></td>
                            <td class="text-end fw-bold text-danger">-<?php echo e(number_format($item->jumlah, 0, ',', '.')); ?></td>
                            <td class="text-end small text-muted">Rp <?php echo e(number_format($item->harga_satuan, 0, ',', '.')); ?></td>
                            <td class="text-end fw-semibold text-dark">Rp <?php echo e(number_format($item->total_nominal, 0, ',', '.')); ?></td>
                            <td class="small text-muted"><?php echo e($item->keterangan ?? '-'); ?></td>
                            <td class="small text-muted"><?php echo e($item->created_at->format('d/m/Y H:i')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot class="table-light fw-bold border-top border-dark">
                        <tr>
                            <td colspan="2" class="text-end">Total Penjualan:</td>
                            <td class="text-end text-danger">-<?php echo e(number_format($totalGobogTerjual, 0, ',', '.')); ?> Gobog</td>
                            <td></td>
                            <td colspan="3">Rp <?php echo e(number_format($totalPenjualan, 0, ',', '.')); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
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
                <div class="fs-5 fw-bold text-dark"><?php echo e(number_format($stock->total_stok, 0, ',', '.')); ?></div>
            </div>
            <div class="col-4">
                <div class="small text-muted">Tersedia di Kasir</div>
                <div class="fs-5 fw-bold text-dark"><?php echo e(number_format($stock->stok_tersedia, 0, ',', '.')); ?></div>
            </div>
            <div class="col-4">
                <div class="small text-muted">Sedang Beredar</div>
                <div class="fs-5 fw-bold text-dark"><?php echo e(number_format($stock->stok_beredar, 0, ',', '.')); ?></div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CENT\Downloads\Program\Gobog\Sistem Gobog\Sistem Gobog New\resources\views/admin/laporan/index.blade.php ENDPATH**/ ?>