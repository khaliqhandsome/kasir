<?php
$title = 'Laporan Penjualan';
$currentPage = 'reports';
require __DIR__ . '/partials/head.php';

$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');

$sales = report_sales($pdo, $from, $to);
$totalTransactions = count($sales);
$totalRevenue = array_sum(array_column($sales, 'total'));
$totalTax = array_sum(array_column($sales, 'tax_amount'));
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan Penjualan</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form class="form-row mb-3">
            <div class="form-group col-md-3">
                <label for="from">Dari</label>
                <input type="date" class="form-control" id="from" name="from" value="<?= htmlspecialchars($from) ?>">
            </div>
            <div class="form-group col-md-3">
                <label for="to">Sampai</label>
                <input type="date" class="form-control" id="to" name="to" value="<?= htmlspecialchars($to) ?>">
            </div>
            <div class="form-group col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Terapkan</button>
            </div>
        </form>
        <div class="row text-center mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-left-primary shadow-sm h-100 py-3">
                    <div class="card-body">
                        <h6 class="text-uppercase text-primary">Transaksi</h6>
                        <h4 class="font-weight-bold mb-0"><?= $totalTransactions ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-left-success shadow-sm h-100 py-3">
                    <div class="card-body">
                        <h6 class="text-uppercase text-success">Total Penjualan</h6>
                        <h4 class="font-weight-bold mb-0"><?= format_money((int) $totalRevenue) ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-left-warning shadow-sm h-100 py-3">
                    <div class="card-body">
                        <h6 class="text-uppercase text-warning">Total Pajak</h6>
                        <h4 class="font-weight-bold mb-0"><?= format_money((int) $totalTax) ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead class="thead-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Invoice</th>
                    <th>Pelanggan</th>
                    <th>Metode</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-right">Pajak</th>
                    <th class="text-right">Total</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($sales)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Belum ada transaksi pada rentang tanggal ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><?= date('d M Y H:i', strtotime($sale['created_at'])) ?></td>
                            <td><?= htmlspecialchars($sale['invoice_number']) ?></td>
                            <td><?= htmlspecialchars($sale['customer_name'] ?: 'Umum') ?></td>
                            <td><?= htmlspecialchars($sale['payment_method'] ?: '-') ?></td>
                            <td class="text-right"><?= format_money((int) $sale['subtotal']) ?></td>
                            <td class="text-right"><?= format_money((int) $sale['tax_amount']) ?></td>
                            <td class="text-right"><?= format_money((int) $sale['total']) ?></td>
                            <td class="text-right"><a href="/receipt.php?id=<?= $sale['id'] ?>" class="btn btn-sm btn-outline-secondary">Struk</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php';
