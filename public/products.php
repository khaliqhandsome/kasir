<?php
$title = 'Produk';
$currentPage = 'products';
require __DIR__ . '/partials/head.php';

$error = '';
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        delete_product($pdo, (int) $_POST['delete_id']);
        flash('success', 'Produk berhasil dihapus.');
        redirect('/products.php');
    }

    $data = [
        'id' => isset($_POST['id']) ? (int) $_POST['id'] : null,
        'name' => trim($_POST['name'] ?? ''),
        'price' => (int) str_replace(['.', ','], '', $_POST['price'] ?? '0'),
        'sku' => trim($_POST['sku'] ?? ''),
    ];

    if ($data['name'] === '' || $data['price'] <= 0) {
        $error = 'Nama dan harga produk wajib diisi.';
        $editing = $data;
    } else {
        upsert_product($pdo, $data);
        flash('success', 'Produk berhasil disimpan.');
        redirect('/products.php');
    }
} elseif (isset($_GET['edit'])) {
    $product = find_product($pdo, (int) $_GET['edit']);
    if ($product) {
        $editing = $product;
    }
}

$products = list_products($pdo);
$successMessage = flash('success');
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kelola Produk</h1>
</div>

<?php if ($successMessage): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($successMessage) ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?= $editing ? 'Edit Produk' : 'Tambah Produk' ?></h6>
            </div>
            <div class="card-body">
                <form method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?= $editing['id'] ?? '' ?>">
                    <div class="form-group">
                        <label for="name">Nama Produk</label>
                        <input type="text" class="form-control" id="name" name="name" required value="<?= input_value($editing ?? [], 'name') ?>">
                        <div class="invalid-feedback">Nama produk wajib diisi.</div>
                    </div>
                    <div class="form-group">
                        <label for="price">Harga Jual</label>
                        <input type="number" min="0" class="form-control" id="price" name="price" required value="<?= input_value($editing ?? [], 'price') ?>">
                        <div class="invalid-feedback">Harga wajib diisi.</div>
                    </div>
                    <div class="form-group">
                        <label for="sku">SKU / Kode</label>
                        <input type="text" class="form-control" id="sku" name="sku" value="<?= input_value($editing ?? [], 'sku') ?>">
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <?php if ($editing): ?>
                            <a href="/products.php" class="btn btn-light">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Produk</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                        <tr>
                            <th>Nama</th>
                            <th>SKU</th>
                            <th class="text-right">Harga</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= htmlspecialchars($product['sku'] ?? '-') ?></td>
                                <td class="text-right"><?= format_money((int) $product['price']) ?></td>
                                <td class="text-right">
                                    <a href="/products.php?edit=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                                        <input type="hidden" name="delete_id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    (function () {
        'use strict';
        window.addEventListener('load', function () {
            var forms = document.getElementsByClassName('needs-validation');
            Array.prototype.filter.call(forms, function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
<?php include __DIR__ . '/partials/footer.php';
