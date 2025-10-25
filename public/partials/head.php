<?php
require_once __DIR__ . '/../../app/bootstrap.php';

$currentPage = $currentPage ?? '';
$title = $title ?? 'Kasir UMKM';
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Aplikasi kasir sederhana untuk usaha kecil berbasis web dengan tampilan mobile friendly.">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        body { font-size: 0.95rem; }
        .sidebar { min-height: 100vh; }
        .table-responsive { overflow-x: auto; }
        @media (max-width: 768px) {
            .sidebar { width: 100%; }
            .sidebar .nav-item .nav-link { padding: 0.75rem 1rem; }
        }
        .btn-print { background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%); color: #fff; }
        .btn-print:hover { color: #fff; filter: brightness(1.05); }
    </style>
</head>
<body id="page-top" class="<?= htmlspecialchars($bodyClass) ?>">
<div id="wrapper">
<?php include __DIR__ . '/sidebar.php'; ?>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include __DIR__ . '/topbar.php'; ?>
        <div class="container-fluid pb-4">
