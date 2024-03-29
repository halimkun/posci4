<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="javascript:void(0)" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <!-- <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">2</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">Infomasi Stok Produk</span>
                <div class="dropdown-divider"></div>
                <a href="javascript:void(0)" class="dropdown-item text-sm">
                    <i class="fas fa-cube mr-2"></i>
                    <span>Minyak Goreng</span>
                    <span class="float-right">Sisa 2 kg</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= base_url('item') ?>" class="dropdown-item dropdown-footer">Lihat Semua</a>
            </div>
        </li> -->
        <?php if (esc(get_user('id_role') != 1)) : ?>
            <li class="nav-item">
                <a class="nav-link" href="/penjualan" title="halaman penjualan">
                    <i class="fas fa-cash-register"></i> <span class="ml-1">Penjualan</span>
                </a>
            </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" href="/penjualan/invoice" title="halaman penjualan">
                <i class="fas fa-file-invoice"></i> <span class="ml-1">Invoice</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link">
                |
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0)" data-toggle="modal" data-target="#modal-logout" role="button">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </li>
    </ul>
</nav>