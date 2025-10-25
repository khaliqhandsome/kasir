<?php
$title = 'Transaksi Baru';
$currentPage = 'transaction';
require __DIR__ . '/partials/head.php';

$products = list_products($pdo);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemsRaw = $_POST['items'] ?? '[]';
    $itemsData = json_decode($itemsRaw, true);

    if (!is_array($itemsData) || empty($itemsData)) {
        $error = 'Tambahkan minimal satu item produk.';
    } else {
        $processedItems = [];
        $subtotal = 0;
        foreach ($itemsData as $item) {
            $quantity = max(1, (int) $item['quantity']);
            $price = (int) $item['price'];
            $name = trim($item['name']);
            if ($price <= 0 || $name === '') {
                continue;
            }
            $total = $price * $quantity;
            $subtotal += $total;
            $processedItems[] = [
                'product_id' => $item['id'] ? (int) $item['id'] : null,
                'product_name' => $name,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $total,
            ];
        }

        if (empty($processedItems)) {
            $error = 'Item produk tidak valid.';
        } else {
            $taxPercent = (int) ($_POST['tax_percent'] ?? 0);
            $taxAmount = (int) round($subtotal * $taxPercent / 100);
            $total = $subtotal + $taxAmount;
            $paid = (int) str_replace(['.', ','], '', $_POST['paid_amount'] ?? '0');

            if ($paid < $total) {
                $error = 'Jumlah pembayaran kurang dari total tagihan.';
            } else {
                $sale = [
                    'invoice_number' => invoice_number(),
                    'customer_name' => trim($_POST['customer_name'] ?? ''),
                    'payment_method' => trim($_POST['payment_method'] ?? ''),
                    'paid_amount' => $paid,
                    'change_amount' => $paid - $total,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'total' => $total,
                    'notes' => trim($_POST['notes'] ?? ''),
                ];

                $saleId = create_sale($pdo, $sale, $processedItems);
                redirect('/receipt.php?id=' . $saleId);
            }
        }
    }
}
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Transaksi Baru</h1>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-body">
        <form method="post" id="sale-form">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="customer_name">Nama Pelanggan</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Opsional">
                </div>
                <div class="form-group col-md-6">
                    <label for="payment_method">Metode Pembayaran</label>
                    <select class="form-control" id="payment_method" name="payment_method">
                        <option value="Tunai">Tunai</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Transfer">Transfer</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-sm" id="items-table">
                    <thead class="thead-light">
                    <tr>
                        <th style="min-width: 160px;">Produk</th>
                        <th style="width: 120px;">Harga</th>
                        <th style="width: 100px;">Qty</th>
                        <th style="width: 140px;">Subtotal</th>
                        <th style="width: 60px;"></th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-item"><i class="fas fa-plus"></i> Tambah Item</button>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tax_percent">PPN / Pajak (%)</label>
                        <input type="number" class="form-control" id="tax_percent" name="tax_percent" value="10" min="0" max="100">
                    </div>
                    <div class="form-group">
                        <label for="notes">Catatan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Contoh: tanpa gula"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-left-primary shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <strong id="subtotal-display">Rp 0</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Pajak (<span id="tax-percent-display">10</span>%)</span>
                                <strong id="tax-display">Rp 0</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total</span>
                                <strong id="total-display">Rp 0</strong>
                            </div>
                            <div class="form-group">
                                <label for="paid_amount">Jumlah Bayar</label>
                                <input type="number" class="form-control" id="paid_amount" name="paid_amount" min="0" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Kembalian</span>
                                <strong id="change-display">Rp 0</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="items" id="items-input">
            <button type="submit" class="btn btn-success btn-block mt-3">Selesaikan Transaksi</button>
        </form>
    </div>
</div>

<script>
    const products = <?= json_encode($products) ?>;

    const tableBody = document.querySelector('#items-table tbody');
    const addItemButton = document.getElementById('add-item');
    const itemsInput = document.getElementById('items-input');
    const subtotalDisplay = document.getElementById('subtotal-display');
    const taxDisplay = document.getElementById('tax-display');
    const totalDisplay = document.getElementById('total-display');
    const changeDisplay = document.getElementById('change-display');
    const taxPercentInput = document.getElementById('tax_percent');
    const taxPercentDisplay = document.getElementById('tax-percent-display');
    const paidAmountInput = document.getElementById('paid_amount');

    function rupiah(amount) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
    }

    function renderOptions(selectedId) {
        return ['<option value="">-- pilih --</option>']
            .concat(products.map(product => `<option value="${product.id}" data-price="${product.price}" ${selectedId == product.id ? 'selected' : ''}>${product.name}</option>`))
            .join('');
    }

    function addRow(item = {}) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select class="form-control form-control-sm product-select">
                    ${renderOptions(item.id || '')}
                </select>
                <input type="text" class="form-control form-control-sm mt-2 product-name" placeholder="Nama custom" value="${item.name || ''}">
            </td>
            <td><input type="number" class="form-control form-control-sm price" min="0" value="${item.price || 0}"></td>
            <td><input type="number" class="form-control form-control-sm quantity" min="1" value="${item.quantity || 1}"></td>
            <td class="text-right font-weight-bold line-total">Rp 0</td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-times"></i></button></td>
        `;
        tableBody.appendChild(row);
        updateRow(row);
    }

    function updateRow(row) {
        const priceInput = row.querySelector('.price');
        const quantityInput = row.querySelector('.quantity');
        const total = (parseInt(priceInput.value || 0, 10)) * (parseInt(quantityInput.value || 0, 10));
        row.querySelector('.line-total').textContent = rupiah(total);
        updateSummary();
    }

    function updateSummary() {
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        let subtotal = 0;
        const payload = [];
        rows.forEach(row => {
            const select = row.querySelector('.product-select');
            const id = parseInt(select.value || 0, 10);
            const nameInput = row.querySelector('.product-name');
            const priceInput = row.querySelector('.price');
            const quantityInput = row.querySelector('.quantity');
            const price = parseInt(priceInput.value || 0, 10);
            const quantity = parseInt(quantityInput.value || 0, 10);
            const productName = nameInput.value || (id ? select.options[select.selectedIndex].text : '');
            const lineTotal = price * quantity;
            subtotal += lineTotal;
            payload.push({ id, name: productName, price, quantity });
        });
        const taxPercent = parseInt(taxPercentInput.value || 0, 10);
        const taxAmount = Math.round(subtotal * taxPercent / 100);
        const total = subtotal + taxAmount;
        const paid = parseInt(paidAmountInput.value || 0, 10);
        const change = Math.max(0, paid - total);

        subtotalDisplay.textContent = rupiah(subtotal);
        taxDisplay.textContent = rupiah(taxAmount);
        totalDisplay.textContent = rupiah(total);
        changeDisplay.textContent = rupiah(change);
        taxPercentDisplay.textContent = taxPercent;
        itemsInput.value = JSON.stringify(payload);
    }

    addItemButton.addEventListener('click', () => addRow());
    taxPercentInput.addEventListener('input', updateSummary);
    paidAmountInput.addEventListener('input', updateSummary);

    tableBody.addEventListener('change', event => {
        if (event.target.classList.contains('product-select')) {
            const select = event.target;
            const priceInput = select.closest('tr').querySelector('.price');
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.dataset.price ? parseInt(selectedOption.dataset.price, 10) : 0;
            if (price) {
                priceInput.value = price;
            }
            updateSummary();
        }
    });

    tableBody.addEventListener('input', event => {
        if (event.target.classList.contains('price') || event.target.classList.contains('quantity') || event.target.classList.contains('product-name')) {
            updateRow(event.target.closest('tr'));
        }
    });

    tableBody.addEventListener('click', event => {
        if (event.target.closest('.remove-item')) {
            const row = event.target.closest('tr');
            row.parentNode.removeChild(row);
            updateSummary();
        }
    });

    document.getElementById('sale-form').addEventListener('submit', function () {
        updateSummary();
    });

    addRow();
</script>
<?php include __DIR__ . '/partials/footer.php';
