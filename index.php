<?php

require_once "config.php";

$current_page = "index.php";

$message = "";
$error = "";


// ============================
// ADD
// ============================

if (isset($_POST["tambah"])) {

    $nama = trim($_POST["nama_barang"] ?? "");
    $jumlah = filter_input(
        INPUT_POST,
        "jumlah",
        FILTER_VALIDATE_INT
    );

    $harga = filter_input(
        INPUT_POST,
        "harga_per_pcs",
        FILTER_VALIDATE_FLOAT
    );


    if ($nama === "") {

        $error = "Nama barang wajib diisi.";

    } elseif ($jumlah === false || $jumlah < 0) {

        $error = "Jumlah stok tidak valid.";

    } elseif ($harga === false || $harga < 0) {

        $error = "Harga barang tidak valid.";

    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO barang
            (nama_barang, jumlah, harga_per_pcs)
            VALUES (?, ?, ?)"
        );

        $stmt->execute([
            $nama,
            $jumlah,
            $harga
        ]);

        header(
            "Location: index.php?success=added"
        );

        exit;

    }

}


// ============================
// EDIT
// ============================

if (isset($_POST["edit"])) {

    $id = filter_input(
        INPUT_POST,
        "id",
        FILTER_VALIDATE_INT
    );

    $nama = trim($_POST["nama_barang"] ?? "");

    $jumlah = filter_input(
        INPUT_POST,
        "jumlah",
        FILTER_VALIDATE_INT
    );

    $harga = filter_input(
        INPUT_POST,
        "harga_per_pcs",
        FILTER_VALIDATE_FLOAT
    );


    if (!$id) {

        $error = "ID barang tidak valid.";

    } elseif ($nama === "") {

        $error = "Nama barang wajib diisi.";

    } elseif ($jumlah === false || $jumlah < 0) {

        $error = "Jumlah stok tidak valid.";

    } elseif ($harga === false || $harga < 0) {

        $error = "Harga barang tidak valid.";

    } else {

        $stmt = $pdo->prepare(
            "UPDATE barang
             SET nama_barang = ?,
                 jumlah = ?,
                 harga_per_pcs = ?
             WHERE id = ?"
        );

        $stmt->execute([
            $nama,
            $jumlah,
            $harga,
            $id
        ]);

        header(
            "Location: index.php?success=edited"
        );

        exit;

    }

}


// ============================
// DELETE
// ============================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    &&
    isset($_POST["hapus"])
) {

    $id = filter_input(
        INPUT_POST,
        "id",
        FILTER_VALIDATE_INT
    );


    if ($id) {

        $stmt = $pdo->prepare(
            "DELETE FROM barang WHERE id = ?"
        );

        $stmt->execute([$id]);

        header(
            "Location: index.php?success=deleted"
        );

        exit;

    }

}


// ============================
// MESSAGE
// ============================

if (isset($_GET["success"])) {

    $messages = [

        "added" =>
            "Barang berhasil ditambahkan.",

        "edited" =>
            "Barang berhasil diperbarui.",

        "deleted" =>
            "Barang berhasil dihapus."

    ];

    $message =
        $messages[$_GET["success"]]
        ?? "";

}


// ============================
// SEARCH
// ============================

$search = trim(
    $_GET["search"] ?? ""
);


if ($search !== "") {

    $stmt = $pdo->prepare(
        "SELECT *
         FROM barang
         WHERE nama_barang LIKE ?
         ORDER BY nama_barang ASC"
    );

    $stmt->execute([
        "%$search%"
    ]);

    $barang = $stmt->fetchAll();

} else {

    $barang = $pdo->query(
        "SELECT *
         FROM barang
         ORDER BY nama_barang ASC"
    )->fetchAll();

}


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

    <title>Inventaris — Gudang</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>

<body>

<div class="app">

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
                class="nav-link"
            >
                <span>⌂</span>
                Beranda
            </a>

            <a
                href="index.php"
                class="nav-link active"
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
            Inventory Management
        </div>

    </aside>


    <div
        class="overlay"
        id="overlay"
    ></div>


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
                    Data Barang
                </h1>

            </div>

            <button
                class="top-action"
                onclick="openAddModal()"
            >
                + Tambah Barang
            </button>

        </header>


        <?php if ($message): ?>

            <div class="alert success-alert">

                ✓
                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>


        <?php if ($error): ?>

            <div class="alert error-alert">

                !
                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <section class="panel">

            <div class="panel-header">

                <div>

                    <p class="panel-label">
                        MASTER DATA
                    </p>

                    <h2>
                        Inventaris Barang
                    </h2>

                </div>

                <span class="badge">
                    <?= count($barang) ?> data
                </span>

            </div>


            <!-- SEARCH -->

            <form
                method="GET"
                class="search-box"
            >

                <input
                    type="search"
                    name="search"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Cari nama barang..."
                >

                <button
                    type="submit"
                    class="search-button"
                >
                    Cari
                </button>

                <?php if ($search !== ""): ?>

                    <a
                        href="index.php"
                        class="clear-button"
                    >
                        Reset
                    </a>

                <?php endif; ?>

            </form>


            <!-- TABLE -->

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
                                Nilai
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if (count($barang)): ?>

                        <?php foreach ($barang as $no => $b): ?>

                            <tr>

                                <td>
                                    <?= $no + 1 ?>
                                </td>

                                <td>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $b["nama_barang"]
                                        ) ?>
                                    </strong>

                                </td>

                                <td>

                                    <?php if ($b["jumlah"] <= 5): ?>

                                        <span class="stock-badge danger">
                                            <?= number_format(
                                                $b["jumlah"]
                                            ) ?>
                                        </span>

                                    <?php else: ?>

                                        <span class="stock-badge">
                                            <?= number_format(
                                                $b["jumlah"]
                                            ) ?>
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>
                                    <?= rupiah(
                                        $b["harga_per_pcs"]
                                    ) ?>
                                </td>

                                <td class="price-cell">

                                    <?= rupiah(
                                        $b["jumlah"]
                                        *
                                        $b["harga_per_pcs"]
                                    ) ?>

                                </td>

                                <td>

                                    <div class="action-group">

                                        <button
                                            class="action edit"
                                            onclick='openEditModal(
                                                <?= json_encode(
                                                    $b,
                                                    JSON_HEX_TAG |
                                                    JSON_HEX_APOS |
                                                    JSON_HEX_QUOT |
                                                    JSON_HEX_AMP
                                                ) ?>
                                            )'
                                        >
                                            Edit
                                        </button>


                                        <form
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus barang ini?')"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= $b["id"] ?>"
                                            >

                                            <button
                                                type="submit"
                                                name="hapus"
                                                class="action delete"
                                            >
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="6"
                                class="empty-table"
                            >

                                <div>
                                    📦
                                </div>

                                <strong>
                                    Belum ada barang
                                </strong>

                                <p>
                                    Tambahkan barang pertama
                                    untuk mulai mengelola inventaris.
                                </p>

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>


<!-- =========================
     ADD MODAL
========================= -->

<div
    class="modal"
    id="addModal"
>

    <div class="modal-box">

        <div class="modal-header">

            <div>

                <p class="panel-label">
                    INVENTARIS
                </p>

                <h2>
                    Tambah Barang
                </h2>

            </div>

            <button
                class="close-modal"
                onclick="closeAddModal()"
            >
                ×
            </button>

        </div>


        <form
            method="POST"
            class="modal-form"
        >

            <label>
                Nama Barang

                <input
                    type="text"
                    name="nama_barang"
                    placeholder="Contoh: Laptop"
                    maxlength="255"
                    required
                >

            </label>


            <label>
                Jumlah

                <input
                    type="number"
                    name="jumlah"
                    min="0"
                    placeholder="0"
                    required
                >

            </label>


            <label>
                Harga per pcs

                <input
                    type="number"
                    name="harga_per_pcs"
                    min="0"
                    step="0.01"
                    placeholder="0"
                    required
                >

            </label>


            <div class="modal-actions">

                <button
                    type="button"
                    class="cancel-button"
                    onclick="closeAddModal()"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    name="tambah"
                    class="save-button"
                >
                    Simpan Barang
                </button>

            </div>

        </form>

    </div>

</div>


<!-- =========================
     EDIT MODAL
========================= -->

<div
    class="modal"
    id="editModal"
>

    <div class="modal-box">

        <div class="modal-header">

            <div>

                <p class="panel-label">
                    INVENTARIS
                </p>

                <h2>
                    Edit Barang
                </h2>

            </div>

            <button
                class="close-modal"
                onclick="closeEditModal()"
            >
                ×
            </button>

        </div>


        <form
            method="POST"
            class="modal-form"
        >

            <input
                type="hidden"
                name="id"
                id="editId"
            >


            <label>
                Nama Barang

                <input
                    type="text"
                    name="nama_barang"
                    id="editNama"
                    maxlength="255"
                    required
                >

            </label>


            <label>
                Jumlah

                <input
                    type="number"
                    name="jumlah"
                    id="editJumlah"
                    min="0"
                    required
                >

            </label>


            <label>
                Harga per pcs

                <input
                    type="number"
                    name="harga_per_pcs"
                    id="editHarga"
                    min="0"
                    step="0.01"
                    required
                >

            </label>


            <div class="modal-actions">

                <button
                    type="button"
                    class="cancel-button"
                    onclick="closeEditModal()"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    name="edit"
                    class="save-button"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>


<script>

const sidebar =
    document.getElementById("sidebar");

const overlay =
    document.getElementById("overlay");

const menuButton =
    document.getElementById("menuButton");


menuButton.onclick = function () {

    sidebar.classList.add("open");

    overlay.classList.add("show");

};


overlay.onclick = function () {

    sidebar.classList.remove("open");

    overlay.classList.remove("show");

};


// ADD

function openAddModal() {

    document
        .getElementById("addModal")
        .classList.add("show");

}


function closeAddModal() {

    document
        .getElementById("addModal")
        .classList.remove("show");

}


// EDIT

function openEditModal(data) {

    document.getElementById(
        "editId"
    ).value = data.id;

    document.getElementById(
        "editNama"
    ).value = data.nama_barang;

    document.getElementById(
        "editJumlah"
    ).value = data.jumlah;

    document.getElementById(
        "editHarga"
    ).value = data.harga_per_pcs;

    document
        .getElementById("editModal")
        .classList.add("show");

}


function closeEditModal() {

    document
        .getElementById("editModal")
        .classList.remove("show");

}


// CLOSE WHEN CLICK OUTSIDE

document
    .querySelectorAll(".modal")
    .forEach(function(modal) {

        modal.addEventListener(
            "click",
            function(e) {

                if (e.target === modal) {

                    modal.classList.remove(
                        "show"
                    );

                }

            }
        );

    });

</script>

</body>

</html>