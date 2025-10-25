<?php
$title = 'Dashboard';
$currentPage = 'dashboard';
require __DIR__ . '/partials/head.php';

$today = date('Y-m-d');
$summary = daily_summary($pdo, $today);
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Ringkasan Hari Ini</h1>
    <a href="/transaction.php" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Transaksi Baru</a>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pendapatan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= format_money($summary['revenue']) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Transaksi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['transactions'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-basket fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Kembalian</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= format_money($summary['change_total']) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-coins fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tanggal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= date('d M Y', strtotime($today)) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Produk Terlaris Hari Ini</h6>
            </div>
            <div class="card-body">
                <?php if (empty($summary['top_products'])): ?>
                    <p class="text-muted mb-0">Belum ada transaksi hari ini.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($summary['top_products'] as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['product_name']) ?></td>
                                        <td class="text-right"><?= $product['qty'] ?></td>
                                        <td class="text-right"><?= format_money((int) $product['amount']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tips Cepat</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><i class="fas fa-mobile-alt text-primary mr-2"></i> Akses langsung dari ponsel tanpa instalasi aplikasi.</p>
                <p class="mb-2"><i class="fas fa-receipt text-primary mr-2"></i> Cetak struk instan setiap selesai transaksi.</p>
                <p class="mb-0"><i class="fas fa-chart-line text-primary mr-2"></i> Pantau performa usaha dengan laporan penjualan sederhana.</p>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php';
