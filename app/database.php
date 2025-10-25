<?php

function database_config(): array
{
    $defaults = [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'kasir',
        'user' => 'root',
        'pass' => '',
    ];

    $configFile = __DIR__ . '/../config/database.php';
    if (file_exists($configFile)) {
        $custom = require $configFile;
        if (is_array($custom)) {
            $defaults = array_merge($defaults, array_intersect_key($custom, $defaults));
        }
    }

    $envMap = [
        'host' => getenv('DB_HOST') ?: null,
        'port' => getenv('DB_PORT') ?: null,
        'name' => getenv('DB_NAME') ?: null,
        'user' => getenv('DB_USER') ?: null,
        'pass' => getenv('DB_PASS'),
    ];

    foreach ($envMap as $key => $value) {
        if ($value !== null && $value !== false) {
            $defaults[$key] = $value;
        }
    }

    return $defaults;
}

function get_connection(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $config = database_config();
        $dsnBase = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $config['host'], $config['port']);
        $dsn = $dsnBase . ';dbname=' . $config['name'];
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Unknown database') !== false) {
                $tmpPdo = new PDO($dsnBase, $config['user'], $config['pass'], $options);
                $tmpPdo->exec(sprintf(
                    'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
                    str_replace('`', '``', $config['name'])
                ));
                $tmpPdo = null;
                $pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
            } else {
                throw $e;
            }
        }
    }

    return $pdo;
}

function ensure_schema(PDO $pdo): void
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS products (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price INT UNSIGNED NOT NULL,
        sku VARCHAR(100) DEFAULT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    $pdo->exec('CREATE TABLE IF NOT EXISTS sales (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        invoice_number VARCHAR(100) NOT NULL,
        customer_name VARCHAR(255) DEFAULT NULL,
        payment_method VARCHAR(100) DEFAULT NULL,
        paid_amount INT UNSIGNED NOT NULL,
        change_amount INT NOT NULL,
        subtotal INT UNSIGNED NOT NULL,
        tax_amount INT UNSIGNED NOT NULL,
        total INT UNSIGNED NOT NULL,
        notes TEXT,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    $pdo->exec('CREATE TABLE IF NOT EXISTS sale_items (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        sale_id INT UNSIGNED NOT NULL,
        product_id INT UNSIGNED DEFAULT NULL,
        product_name VARCHAR(255) NOT NULL,
        quantity INT UNSIGNED NOT NULL,
        price INT UNSIGNED NOT NULL,
        total INT UNSIGNED NOT NULL,
        CONSTRAINT fk_sale_items_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
        CONSTRAINT fk_sale_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    if ((int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn() === 0) {
        $seed = [
            ['Es Kopi Susu', 18000],
            ['Kopi Hitam', 12000],
            ['Teh Tarik', 15000],
            ['Roti Bakar Coklat', 20000],
            ['Mie Goreng Spesial', 25000],
        ];

        $stmt = $pdo->prepare('INSERT INTO products (name, price, sku) VALUES (:name, :price, :sku)');
        foreach ($seed as $index => [$name, $price]) {
            $stmt->execute([
                ':name' => $name,
                ':price' => $price,
                ':sku' => 'SKU' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
            ]);
        }
    }
}

function list_products(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM products ORDER BY name ASC')->fetchAll();
}

function find_product(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();
    return $product ?: null;
}

function upsert_product(PDO $pdo, array $data): void
{
    if (!empty($data['id'])) {
        $stmt = $pdo->prepare('UPDATE products SET name = :name, price = :price, sku = :sku WHERE id = :id');
        $stmt->execute([
            ':name' => $data['name'],
            ':price' => $data['price'],
            ':sku' => $data['sku'] ?: null,
            ':id' => $data['id'],
        ]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO products (name, price, sku) VALUES (:name, :price, :sku)');
        $stmt->execute([
            ':name' => $data['name'],
            ':price' => $data['price'],
            ':sku' => $data['sku'] ?: null,
        ]);
    }
}

function delete_product(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

function create_sale(PDO $pdo, array $sale, array $items): int
{
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare('INSERT INTO sales (
            invoice_number, customer_name, payment_method, paid_amount, change_amount,
            subtotal, tax_amount, total, notes
        ) VALUES (:invoice_number, :customer_name, :payment_method, :paid_amount, :change_amount,
            :subtotal, :tax_amount, :total, :notes)');

        $stmt->execute([
            ':invoice_number' => $sale['invoice_number'],
            ':customer_name' => $sale['customer_name'] ?: null,
            ':payment_method' => $sale['payment_method'] ?: null,
            ':paid_amount' => $sale['paid_amount'],
            ':change_amount' => $sale['change_amount'],
            ':subtotal' => $sale['subtotal'],
            ':tax_amount' => $sale['tax_amount'],
            ':total' => $sale['total'],
            ':notes' => $sale['notes'] ?: null,
        ]);

        $saleId = (int) $pdo->lastInsertId();

        $stmtItem = $pdo->prepare('INSERT INTO sale_items (
            sale_id, product_id, product_name, quantity, price, total
        ) VALUES (:sale_id, :product_id, :product_name, :quantity, :price, :total)');

        foreach ($items as $item) {
            $stmtItem->execute([
                ':sale_id' => $saleId,
                ':product_id' => $item['product_id'] ?: null,
                ':product_name' => $item['product_name'],
                ':quantity' => $item['quantity'],
                ':price' => $item['price'],
                ':total' => $item['total'],
            ]);
        }

        $pdo->commit();
        return $saleId;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function find_sale(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM sales WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $sale = $stmt->fetch();
    if (!$sale) {
        return null;
    }

    $stmtItems = $pdo->prepare('SELECT * FROM sale_items WHERE sale_id = :sale_id');
    $stmtItems->execute([':sale_id' => $id]);
    $sale['items'] = $stmtItems->fetchAll();

    return $sale;
}

function daily_summary(PDO $pdo, string $date): array
{
    $stmt = $pdo->prepare('SELECT COUNT(*) as transactions, COALESCE(SUM(total), 0) as revenue, COALESCE(SUM(change_amount), 0) as change_total
        FROM sales WHERE DATE(created_at) = :date');
    $stmt->execute([':date' => $date]);
    $summary = $stmt->fetch();

    $stmtProducts = $pdo->prepare('SELECT product_name, SUM(quantity) as qty, SUM(total) as amount
        FROM sale_items si
        JOIN sales s ON s.id = si.sale_id
        WHERE DATE(s.created_at) = :date
        GROUP BY product_name
        ORDER BY qty DESC
        LIMIT 5');
    $stmtProducts->execute([':date' => $date]);

    return [
        'transactions' => (int) $summary['transactions'],
        'revenue' => (int) $summary['revenue'],
        'change_total' => (int) $summary['change_total'],
        'top_products' => $stmtProducts->fetchAll(),
    ];
}

function report_sales(PDO $pdo, string $from, string $to): array
{
    $stmt = $pdo->prepare('SELECT * FROM sales WHERE DATE(created_at) BETWEEN :from AND :to ORDER BY created_at DESC');
    $stmt->execute([':from' => $from, ':to' => $to]);
    return $stmt->fetchAll();
}

function format_money(int $amount): string
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
