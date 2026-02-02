<?php
$host = "localhost";
$db   = "gudang";
$user = "root";
$password = "";

try{
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("Koneksi gagal: " . $e->getMessage());
}

if (isset($_POST['tambah'])){
    $nama  = $_POST['nama_barang'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga_per_pcs'];

    $stmt = $pdo->prepare("INSERT INTO barang (nama_barang, jumlah, harga_per_pcs) VALUES (?, ?, ?)");
    $stmt->execute([$nama, $jumlah, $harga]);
    header("Location: index.php");
    exit;
}

if (isset($_POST['edit'])){
    $id = $_POST['id'];
    $nama = $_POST['nama_barang'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga_per_pcs'];

    $stmt = $pdo->prepare("UPDATE barang SET nama_barang=?, jumlah=?, harga_per_pcs=? WHERE id=?");
    $stmt->execute([$nama, $jumlah, $harga, $id]);
    header("Location: index.php");
    exit;
}

if (isset($_GET['hapus'])){
    $stmt = $pdo->prepare("DELETE FROM barang WHERE id=?");
    $stmt->execute([$_GET['hapus']]);
    header("Location: index.php");
    exit;
}

$search = $_GET['search'] ?? "";
$stmt = $pdo->prepare("SELECT * FROM barang WHERE nama_barang LIKE ? ORDER BY nama_barang ASC");
$stmt->execute(["%$search%"]);
$barang = $stmt->fetchAll();
$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inventaris Gudang</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{ 
    overflow-x: hidden;
}
.sidebar{
    position: fixed;
    top: 0;
    left: -25%;
    width: 25%;
    height: 100%;
    background-color: #343a40;
    padding-top: 20px;
    transition: 0.4s;
    z-index: 1000;
}
.sidebar.active{ 
    left: 0;
}
.sidebar a{ 
    display: block; 
    padding: 10px;
    font-size: 18px; 
    text-decoration: none;
}
.sidebar a:hover{
    background: #495057; 
    border-radius: 5px;
}
.sidebar a.active{
    background-color: #495057;
    border-radius: 5px;
}
.content{
    transition: 0.4s;
    padding-left: 15px;  
    padding-right: 15px; 
}
.content.shift{
    padding-left: calc(15px + 25%); 
    padding-right: 15px;           
}
.content table{
    width: 100% !important;
    table-layout: auto;
}
thead tr{
    background: #e6e6e6;
}
tbody tr{ 
    background: white; 
}
</style>
</head>
<body>
    <div id="sidebar" class="sidebar">
        <h4 class="text-white text-center mt-4">Menu</h4>
        <ul class="list-unstyled px-3 mt-3">
            <li><a href="#" class="text-white <?= ($current_page == 'beranda.php') ? 'active' : '' ?>">Beranda</a></li>
            <li><a href="index.php" class="text-white <?= ($current_page == 'index.php') ? 'active' : '' ?>">Inventaris</a></li>
            <li><a href="#" class="text-white <?= ($current_page == 'laporan.php') ? 'active' : '' ?>">Laporan</a></li>
        </ul>
    </div>

    <button class="btn btn-dark m-3" id="openSidebar">☰</button>
    <div id="content" class="content container mt-5 pt-3">
    <h2 class="text-center mb-4">Data barang الإخلاص</h2>

    <form class="d-flex justify-content-center mb-4" method="GET">
        <input type="text" class="form-control w-50" placeholder="Cari barang" name="search" value="<?= $search ?>">
        <button type="submit" class="btn btn-primary ms-2">Cari</button>
        <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Barang</button>
    </form>

    <table class="table table-bordered table-striped text-center">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Harga / pcs</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;?>
            <?php foreach ($barang as $b): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                <td><?= $b['jumlah'] ?></td>
                <td>Rp <?= number_format($b['harga_per_pcs'],0,',','.') ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $b['id'] ?>">Edit</button>
                    <a href="index.php?hapus=<?= $b['id'] ?>" onclick="return confirm('Hapus data ini?')" class="btn btn-danger btn-sm">Hapus</a>
                </td>
            </tr>

            <div class="modal fade" id="modalEdit<?= $b['id'] ?>">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5>Edit Barang</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                <label>Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control mb-3" value="<?= $b['nama_barang'] ?>" required>
                                <label>Jumlah</label>
                                <input type="number" name="jumlah" class="form-control mb-3" value="<?= $b['jumlah'] ?>" required>
                                <label>Harga / pcs</label>
                                <input type="text" name="harga_per_pcs" class="form-control mb-3" value="<?= $b['harga_per_pcs'] ?>" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5>Tambah Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control mb-3" required>
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" class="form-control mb-3" required>
                    <label>Harga per pcs</label>
                    <input type="text" name="harga_per_pcs" class="form-control mb-3" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.getElementById("sidebar");
const content = document.getElementById("content");
const openBtn = document.getElementById("openSidebar");

openBtn.onclick = () =>{
    sidebar.classList.add("active");
    content.classList.add("shift");
};
window.onclick = (e) =>{
    if (!sidebar.contains(e.target) && e.target !== openBtn){
        sidebar.classList.remove("active");
        content.classList.remove("shift");
    }
};
</script>
</body>
</html>
