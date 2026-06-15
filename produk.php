<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'pedia_clone');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data pengguna berdasarkan username dari session
$username = $_SESSION['username'];
$sql_user = "SELECT id,role FROM tb_users WHERE username = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $id_penjual = $user['id'];
} else {
    echo "<script>alert('Data pengguna tidak ditemukan. Silakan login ulang.');
    window.location.href = 'login.php';</script>";
    exit;
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($user['role'] == 'pembeli') {
    header("Location: index.php");
    exit;
}

$stmt_user->close();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $berat_produk = $_POST['berat_produk'];
    $stok = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];
    $id_penjual;
    $foto_produk = $_FILES['foto_produk']['name'];
    $target_dir = "images/";
    $target_file = $target_dir . basename($foto_produk);

    // Buat direktori jika belum ada
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Upload file
    if (move_uploaded_file($_FILES['foto_produk']['tmp_name'], $target_file)) {
        // Koneksi ke database
        $conn = new mysqli('localhost', 'root', '', 'pedia_clone');

        // Periksa koneksi
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Simpan data produk ke database
        $sql = "INSERT INTO tb_produk (nama_produk, harga, berat_produk, stok, deksripsi, id_penjual, foto_produk) 
                    VALUES ('$nama_produk', '$harga', '$berat_produk', '$stok', '$deskripsi', '$id_penjual', '$foto_produk')";

        if ($conn->query($sql) === TRUE) {
            echo "Produk baru berhasil ditambahkan.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    } else {
        echo "Maaf, terjadi kesalahan saat mengunggah file.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Tambah Produk Baru</h2>
        <form action="produk.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nama_produk">Nama Produk:</label>
                <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
            </div>
            <div class="form-group">
                <label for="harga">Harga:</label>
                <input type="number" class="form-control" id="harga" name="harga" required>
            </div>
            <div class="form-group">
                <label for="berat_produk">Berat Produk (gram):</label>
                <input type="number" class="form-control" id="berat_produk" name="berat_produk" required>
            </div>
            <div class="form-group">
                <label for="stok">Stok:</label>
                <input type="number" class="form-control" id="stok" name="stok" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" required></textarea>
            </div>
            <div class="form-group">
                <label for="foto_produk">Foto Produk:</label>
                <input type="file" class="form-control" id="foto_produk" name="foto_produk" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Produk</button>
        </form>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>