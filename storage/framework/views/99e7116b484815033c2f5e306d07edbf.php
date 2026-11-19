<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Sistem Gobog'); ?> - Pasar Preng Sewu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-w: 240px; }
        body { background: #f6f8fa; min-height: 100vh; color: #212529; }

        #sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            height: 100dvh;
            background: #181c24;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1050;
            transition: transform .25s ease;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-right: 1px solid rgba(255,255,255,.05);
        }
        #sidebar .sidebar-brand {
            padding: 20px 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            flex-shrink: 0;
        }
        #sidebar .sidebar-brand h5 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: 1rem;
            letter-spacing: .02em;
        }
        #sidebar .sidebar-brand small { color: rgba(255,255,255,.45); font-size: .72rem; }

        #sidebar nav {
            flex: 1 1 0;
            min-height: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        #sidebar .nav-section {
            padding: 10px 14px 4px;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .08em;
            color: rgba(255,255,255,.35);
            text-transform: uppercase;
        }
        #sidebar .nav-link {
            color: rgba(255,255,255,.65);
            border-radius: 6px;
            margin: 2px 10px;
            padding: 8px 12px;
            font-size: .875rem;
            transition: background .15s, color .15s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        #sidebar .nav-link:hover { background: rgba(255,255,255,.06); color: #fff; }
        #sidebar .nav-link.active {
            background: #2a313d;
            color: #fff;
            font-weight: 600;
        }
        #sidebar .nav-link i { width: 18px; text-align: center; font-size: 1rem; }

        #sidebar .sidebar-footer {
            margin-top: auto;
            padding: 14px 12px;
            padding-bottom: calc(14px + env(safe-area-inset-bottom, 0px));
            border-top: 1px solid rgba(255,255,255,.08);
            flex-shrink: 0;
        }
        #sidebar .user-info { color: rgba(255,255,255,.75); font-size: .8rem; }
        #sidebar .user-info .name { color: #fff; font-weight: 600; font-size: .875rem; }

        #main { margin-left: var(--sidebar-w); min-height: 100vh; }
        #topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .page-content { padding: 24px; }

        .card {
            border: 1px solid #e5e7eb !important;
            background: #fff;
            overflow: hidden;
        }
        .table thead th {
            background-color: #181c24 !important;
            color: #ffffff !important;
            font-weight: 600;
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            border-bottom: 1px solid #2a313d !important;
            border-top: none !important;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .dataTables_wrapper {
            padding: 14px 16px 8px;
        }
        .dataTables_wrapper .row:first-child {
            margin-bottom: 10px;
        }
        .dataTables_wrapper .row:last-child {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #f1f3f5;
        }
        .dataTables_length label,
        .dataTables_filter label {
            font-size: .85rem;
            color: #495057;
            font-weight: 500;
        }
        .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: .875rem;
        }
        .dataTables_filter input:focus {
            border-color: #181c24;
            box-shadow: 0 0 0 .2rem rgba(24,28,36,.15);
            outline: 0;
        }
        .dataTables_length select {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 4px 26px 4px 10px;
            font-size: .875rem;
        }
        .dataTables_info {
            font-size: .8rem;
            color: #6c757d;
            padding-top: 8px !important;
        }
        .dataTables_paginate .pagination {
            margin-bottom: 0;
        }
        .dataTables_paginate .page-link {
            font-size: .8rem;
            color: #181c24;
            border-radius: 4px;
            margin: 0 2px;
            padding: 4px 10px;
        }
        .dataTables_paginate .page-item.active .page-link {
            background-color: #181c24 !important;
            border-color: #181c24 !important;
            color: #fff !important;
        }
        .dataTables_paginate .page-link:hover {
            background-color: #e9ecef;
            color: #181c24;
        }

        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main { margin-left: 0; }
            #overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 1040; }
            #overlay.show { display: block; }
            .page-content { padding: 16px; }
        }
    </style>
</head>
<body>

<div id="sidebar">
    <div class="sidebar-brand d-flex justify-content-between align-items-center">
        <div>
            <h5><i class="bi bi-coin me-2"></i>Sistem Gobog</h5>
            <small>Pasar Preng Sewu</small>
        </div>
        <button type="button" class="btn btn-sm btn-link text-white-50 d-lg-none p-0 text-decoration-none" onclick="closeSidebar()" aria-label="Tutup">
            <i class="bi bi-x-lg fs-5"></i>
        </button>
    </div>

    <nav class="mt-2 flex-grow-1">
        <?php if(auth()->guard()->check()): ?>
            <div class="nav-section">Menu Utama</div>
            <a href="<?php echo e(route('admin.dashboard')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="<?php echo e(route('admin.stok.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.stok.*') ? 'active' : ''); ?>">
                <i class="bi bi-coin"></i> Stok Gobog
            </a>

            <div class="nav-section mt-2">Transaksi</div>
            <a href="<?php echo e(route('admin.penjualan.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.penjualan.*') ? 'active' : ''); ?>">
                <i class="bi bi-bag-check"></i> Penjualan (Keluar)
            </a>
            <a href="<?php echo e(route('admin.pengembalian.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.pengembalian.*') ? 'active' : ''); ?>">
                <i class="bi bi-arrow-return-left"></i> Pengembalian (Masuk)
            </a>
            <a href="<?php echo e(route('admin.laporan.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.laporan.*') ? 'active' : ''); ?>">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan
            </a>

            <div class="nav-section mt-2">Master &amp; Sistem</div>
            <a href="<?php echo e(route('admin.tenan.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.tenan.*') ? 'active' : ''); ?>">
                <i class="bi bi-shop"></i> Kelola Tenan
            </a>
            <a href="<?php echo e(route('admin.users.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
                <i class="bi bi-people"></i> Kelola Admin
            </a>
            <a href="<?php echo e(route('admin.pengaturan.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('admin.pengaturan.*') ? 'active' : ''); ?>">
                <i class="bi bi-gear"></i> Pengaturan
            </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <?php if(auth()->guard()->check()): ?>
            <div class="user-info mb-2">
                <div class="name"><i class="bi bi-person-circle me-1"></i><?php echo e(auth()->user()->nama); ?></div>
                <span class="badge bg-secondary-subtle text-secondary border mt-1">Admin</span>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-sm btn-outline-light w-100 opacity-75">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div id="overlay" onclick="closeSidebar()"></div>

<div id="main">
    <div id="topbar" class="d-flex align-items-center">
        <button class="btn btn-sm btn-outline-secondary d-lg-none me-3" onclick="toggleSidebar()">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="fw-semibold text-muted small"><?php echo $__env->yieldContent('title', 'Dashboard'); ?></span>
    </div>

    <div class="page-content">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
    document.getElementById('overlay').classList.toggle('show');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('show');
    document.getElementById('overlay').classList.remove('show');
}

$(document).ready(function() {
    $('.datatable').each(function() {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang cocok",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                },
                pageLength: 10,
                autoWidth: false,
                order: []
            });
        }
    });
});
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\CENT\Downloads\Program\Gobog\Sistem Gobog\Sistem Gobog New\resources\views/layouts/app.blade.php ENDPATH**/ ?>