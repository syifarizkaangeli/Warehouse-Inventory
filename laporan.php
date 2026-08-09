<?php

require_once "config.php";

// Ambil semua data barang
$stmt = $pdo->query("
    SELECT
        id,
        nama_barang,
        jumlah,
        harga_per_pcs,
        (jumlah * harga_per_pcs) AS total_nilai
    FROM barang
    ORDER BY nama_barang ASC
");

$barang = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Hitung total
$totalJenis = count($barang);
$totalStok = 0;
$totalNilai = 0;

foreach ($barang as $item) {
    $totalStok += (int) $item["jumlah"];
    $totalNilai += (float) $item["total_nilai"];
}


// Format Rupiah
function rupiah($angka)
{
    return "Rp " . number_format(
        $angka,
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

    <title>Laporan Inventaris - Gudang</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body>

<div class="app">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">

            <div class="brand-icon">
                G
            </div>

            <div>
                <strong>Gudang</strong>

                <span>
                    Inventory System
                </span>
            </div>

        </div>


        <nav>

            <a
                href="beranda.php"
                class="nav-link"
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
                class="nav-link active"
            >
                <span>▤</span>
                Laporan
            </a>

        </nav>


        <div class="sidebar-bottom">

            Inventory Management

        </div>

    </aside>


    <!-- MAIN -->
    <main class="main">

        <!-- HEADER -->
        <header class="topbar">

            <div>

                <p class="eyebrow">
                    REPORT
                </p>

                <h1>
                    Laporan Inventaris
                </h1>

            </div>


            <button
                type="button"
                class="top-action no-print"
                onclick="window.print()"
            >
                🖨 Print Laporan
            </button>

        </header>


        <!-- SUMMARY -->
        <section class="report-summary">


            <div>

                <span>
                    Total Jenis Barang
                </span>

                <strong>
                    <?= number_format($totalJenis) ?>
                </strong>

            </div>


            <div>

                <span>
                    Total Stok
                </span>

                <strong>
                    <?= number_format($totalStok) ?>
                </strong>

            </div>


            <div>

                <span>
                    Total Nilai Inventaris
                </span>

                <strong>
                    <?= rupiah($totalNilai) ?>
                </strong>

            </div>


        </section>


        <!-- REPORT TABLE -->
        <section class="panel">


            <div class="panel-header">

                <div>

                    <p class="panel-label">
                        INVENTORY REPORT
                    </p>

                    <h2>
                        Daftar Barang
                    </h2>

                </div>


                <span class="badge">

                    <?= date("d/m/Y") ?>

                </span>

            </div>


            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>
                                No
                            </th>

                            <th>
                                Nama Barang
                            </th>

                            <th>
                                Stok
                            </th>

                            <th>
                                Harga / pcs
                            </th>

                            <th>
                                Total Nilai
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if (count($barang) > 0): ?>


                        <?php foreach ($barang as $index => $item): ?>

                            <tr>

                                <td>
                                    <?= $index + 1 ?>
                                </td>


                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $item["nama_barang"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>

                                    </strong>

                                </td>


                                <td>

                                    <?= number_format(
                                        $item["jumlah"]
                                    ) ?>

                                </td>


                                <td>

                                    <?= rupiah(
                                        $item["harga_per_pcs"]
                                    ) ?>

                                </td>


                                <td class="price-cell">

                                    <?= rupiah(
                                        $item["total_nilai"]
                                    ) ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="5"
                                class="empty-table"
                            >

                                <div>
                                    📦
                                </div>

                                <strong>
                                    Belum ada data barang
                                </strong>

                                <p>
                                    Silakan tambahkan barang
                                    terlebih dahulu melalui
                                    halaman Inventaris.
                                </p>

                            </td>

                        </tr>


                    <?php endif; ?>

                    </tbody>


                    <?php if (count($barang) > 0): ?>

                        <tfoot>

                            <tr>

                                <th colspan="2">
                                    TOTAL
                                </th>

                                <th>
                                    <?= number_format(
                                        $totalStok
                                    ) ?>
                                </th>

                                <th>
                                    -
                                </th>

                                <th>
                                    <?= rupiah(
                                        $totalNilai
                                    ) ?>
                                </th>

                            </tr>

                        </tfoot>

                    <?php endif; ?>


                </table>

            </div>

        </section>


    </main>

</div>

</body>

</html>