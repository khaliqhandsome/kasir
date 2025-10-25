<?php
$menus = [
    ['label' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/index.php', 'key' => 'dashboard'],
    ['label' => 'Transaksi Baru', 'icon' => 'fas fa-cash-register', 'url' => '/transaction.php', 'key' => 'transaction'],
    ['label' => 'Produk', 'icon' => 'fas fa-boxes', 'url' => '/products.php', 'key' => 'products'],
    ['label' => 'Laporan', 'icon' => 'fas fa-chart-line', 'url' => '/reports.php', 'key' => 'reports'],
];
?>
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-receipt"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Kasir UMKM</div>
    </a>

    <hr class="sidebar-divider my-0">

    <?php foreach ($menus as $menu): ?>
        <li class="nav-item <?= active_menu($currentPage, $menu['key']) ?>">
            <a class="nav-link" href="<?= $menu['url'] ?>">
                <i class="<?= $menu['icon'] ?>"></i>
                <span><?= htmlspecialchars($menu['label']) ?></span></a>
        </li>
    <?php endforeach; ?>

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
