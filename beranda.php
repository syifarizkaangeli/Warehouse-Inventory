<?php

require_once "config.php";

$current_page = "beranda.php";

$totalJenis = $pdo->query(
    "SELECT COUNT(*) FROM barang"
)->fetchColumn();

$totalStok = $pdo->query(
    "SELECT COALESCE(SUM(jumlah), 0) FROM barang"
)->fetchColumn();

$totalNilai = $pdo->query(
    "SELECT COALESCE(SUM(jumlah * harga_per_pcs), 0)
     FROM barang"
)->fetchColumn();

$stokRendah = $pdo->query(
    "SELECT *
     FROM barang
     WHERE jumlah <= 5
     ORDER BY jumlah ASC, nama_barang ASC"
)->fetchAll();

$barangTerbaru = $pdo->query(
    "SELECT *
     FROM barang
     ORDER BY created_at DESC
     LIMIT 5"
)->fetchAll();

function rupiah($value)
{
    return "Rp " . number_format(
        $value,
        0,
        ",",
        "."
    );
}

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard — Gudang</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>

<body>

<div class="app">

    <!-- SIDEBAR -->

    <aside class="sidebar" id="sidebar">

        <div class="brand">

            <div class="brand-icon">
                G
            </div>

            <div>
                <strong>Gudang</strong>
                <span>Inventory System</span>
            </div>

        </div>


        <nav>

            <a
                href="beranda.php"
                class="nav-link active"
            >
                <span>⌂</span>
                Beranda
            </a>

            <a
                href="index.php"
                class="nav-link"
            >
                <span>▣</span>
                Inventaris
            </a>

            <a
                href="laporan.php"
                class="nav-link"
            >
                <span>▤</span>
                Laporan
            </a>

        </nav>


        <div class="sidebar-bottom">

            <span>
                Inventory Management
            </span>

        </div>

    </aside>


    <div
        class="overlay"
        id="overlay"
    ></div>


    <!-- MAIN -->

    <main class="main">

        <header class="topbar">

            <button
                class="menu-button"
                id="menuButton"
            >
                ☰
            </button>

            <div>

                <p class="eyebrow">
                    INVENTORY
                </p>

                <h1>
                    Dashboard
                </h1>

            </div>

            <a
                href="index.php?action=tambah"
                class="top-action"
            >
                + Tambah Barang
            </a>

        </header>


        <!-- STATISTICS -->

        <section class="stats-grid">

            <div class="stat-card">

                <div class="stat-icon">
                    📦
                </div>

                <div>

                    <span>
                        Jenis Barang
                    </span>

                    <strong>
                        <?= number_format($totalJenis) ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    📊
                </div>

                <div>

                    <span>
                        Total Stok
                    </span>

                    <strong>
                        <?= number_format($totalStok) ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    💰
                </div>

                <div>

                    <span>
                        Nilai Inventaris
                    </span>

                    <strong>
                        <?= rupiah($totalNilai) ?>
                    </strong>

                </div>

            </div>

        </section>


        <div class="dashboard-grid">

            <!-- LOW STOCK -->

            <section class="panel">

                <div class="panel-header">

                    <div>

                        <p class="panel-label">
                            PERHATIAN
                        </p>

                        <h2>
                            Stok Rendah
                        </h2>

                    </div>

                    <span class="badge warning">
                        <?= count($stokRendah) ?> barang
                    </span>

                </div>


                <?php if (count($stokRendah) > 0): ?>

                    <div class="stock-list">

                        <?php foreach ($stokRendah as $item): ?>

                            <div class="stock-item">

                                <div>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $item["nama_barang"]
                                        ) ?>
                                    </strong>

                                    <small>
                                        <?= rupiah(
                                            $item["harga_per_pcs"]
                                        ) ?>/pcs
                                    </small>

                                </div>

                                <span class="stock-danger">

                                    <?= number_format(
                                        $item["jumlah"]
                                    ) ?>

                                    tersisa

                                </span>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <div class="empty-state">

                        <span>✓</span>

                        <p>
                            Semua stok masih aman.
                        </p>

                    </div>

                <?php endif; ?>

            </section>


            <!-- RECENT -->

            <section class="panel">

                <div class="panel-header">

                    <div>

                        <p class="panel-label">
                            INVENTARIS
                        </p>

                        <h2>
                            Barang Terbaru
                        </h2>

                    </div>

                    <a
                        href="index.php"
                        class="text-link"
                    >
                        Lihat semua →
                    </a>

                </div>


                <div class="mini-list">

                    <?php foreach ($barangTerbaru as $item): ?>

                        <div class="mini-item">

                            <div class="mini-icon">
                                📦
                            </div>

                            <div>

                                <strong>
                                    <?= htmlspecialchars(
                                        $item["nama_barang"]
                                    ) ?>
                                </strong>

                                <small>
                                    <?= number_format(
                                        $item["jumlah"]
                                    ) ?>
                                    pcs
                                </small>

                            </div>

                            <span>
                                <?= rupiah(
                                    $item["harga_per_pcs"]
                                ) ?>
                            </span>

                        </div>

                    <?php endforeach; ?>


                    <?php if (count($barangTerbaru) === 0): ?>

                        <div class="empty-state">

                            <span>📦</span>

                            <p>
                                Belum ada barang.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </section>

        </div>

    </main>

</div>


<script>

const sidebar =
    document.getElementById("sidebar");

const overlay =
    document.getElementById("overlay");

const menuButton =
    document.getElementById("menuButton");


function openMenu() {

    sidebar.classList.add("open");

    overlay.classList.add("show");

}


function closeMenu() {

    sidebar.classList.remove("open");

    overlay.classList.remove("show");

}


menuButton.addEventListener(
    "click",
    openMenu
);


overlay.addEventListener(
    "click",
    closeMenu
);

</script>

</body>

</html>