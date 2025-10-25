<?php
$title = 'Cetak Struk';
$currentPage = 'transaction';
require __DIR__ . '/partials/head.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$sale = $id ? find_sale($pdo, $id) : null;

if (!$sale) {
    echo '<div class="alert alert-danger">Struk tidak ditemukan.</div>';
    include __DIR__ . '/partials/footer.php';
    exit;
}
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Struk Pembayaran</h1>
    <button class="btn btn-print shadow-sm"><i class="fas fa-print"></i> Cetak</button>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="font-weight-bold mb-1">Kasir UMKM</h5>
                        <p class="mb-0 text-muted">Struk #<?= htmlspecialchars($sale['invoice_number']) ?></p>
                    </div>
                    <span class="badge badge-primary">Lunas</span>
                </div>
                <div class="border-top pt-3">
                    <p class="mb-1"><strong>Pelanggan:</strong> <?= htmlspecialchars($sale['customer_name'] ?: 'Umum') ?></p>
                    <p class="mb-1"><strong>Tanggal:</strong> <?= date('d M Y H:i', strtotime($sale['created_at'])) ?></p>
                    <p class="mb-3"><strong>Metode:</strong> <?= htmlspecialchars($sale['payment_method'] ?: 'Tunai') ?></p>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Harga</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($sale['items'] as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td class="text-right"><?= $item['quantity'] ?></td>
                                    <td class="text-right"><?= format_money((int) $item['price']) ?></td>
                                    <td class="text-right"><?= format_money((int) $item['total']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <strong><?= format_money((int) $sale['subtotal']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Pajak</span>
                        <strong><?= format_money((int) $sale['tax_amount']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total</span>
                        <strong><?= format_money((int) $sale['total']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Bayar</span>
                        <strong><?= format_money((int) $sale['paid_amount']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Kembalian</span>
                        <strong><?= format_money((int) $sale['change_amount']) ?></strong>
                    </div>
                    <?php if (!empty($sale['notes'])): ?>
                        <p class="text-muted small mb-0">Catatan: <?= nl2br(htmlspecialchars($sale['notes'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: #fff !important; }
    .sidebar, .topbar, .btn-print, footer, .scroll-to-top { display: none !important; }
    #content { margin: 0 !important; padding: 0 !important; }
    .card { box-shadow: none !important; border: none !important; }
}
</style>
<?php include __DIR__ . '/partials/footer.php';
