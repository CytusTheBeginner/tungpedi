<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'pedia_clone');

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil keranjang dari session
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}
$keranjang = $_SESSION['keranjang'];

// Proses pengurangan dan penambahan jumlah produk
if (isset($_GET['action'])) {
    $id_produk = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Query untuk mengambil data produk berdasarkan ID
    $sql = "SELECT * FROM tb_produk WHERE id_produk = $id_produk";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $produk = $result->fetch_assoc();

        if ($_GET['action'] == 'tambah') {
            // Cari produk di keranjang
            $item_exists = false;
            foreach ($keranjang as $index => $item) {
                if ($item['id_produk'] == $id_produk) {
                    // Tambah jumlah produk jika sudah ada di keranjang
                    $_SESSION['keranjang'][$index]['quantity'] += 1;
                    $item_exists = true;
                    break;
                }
            }

            // Jika produk tidak ada di keranjang, tambahkan produk baru
            if (!$item_exists) {
                $_SESSION['keranjang'][] = [
                    'id_produk' => $produk['id_produk'],
                    'nama_produk' => $produk['nama_produk'],
                    'harga' => $produk['harga'],
                    'quantity' => 1,
                    'berat_produk' => $produk['berat_produk']
                ];
            }
        } elseif ($_GET['action'] == 'kurang') {
            // Kurangi jumlah produk di keranjang
            foreach ($keranjang as $index => $item) {
                if ($item['id_produk'] == $id_produk && $_SESSION['keranjang'][$index]['quantity'] > 1) {
                    $_SESSION['keranjang'][$index]['quantity'] -= 1;
                    break;
                }
            }
        } elseif ($_GET['action'] == 'hapus') {
            // Hapus produk dari keranjang
            foreach ($keranjang as $index => $item) {
                if ($item['id_produk'] == $id_produk) {
                    unset($_SESSION['keranjang'][$index]);
                    $_SESSION['keranjang'] = array_values($_SESSION['keranjang']); // Reset indeks array
                    break;
                }
            }
        }
    }
}

// Refresh data keranjang setelah operasi
$keranjang = $_SESSION['keranjang'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,500;0,700;1,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />

    <!-- bs icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

    <!-- icons -->
    <script src="https://unpkg.com/feather-icons"></script>

    <!-- icons web -->
    <link rel="icon" type="image" href="./assets/img/tokped.png" />

    <!-- CSS -->
    <link rel="stylesheet" href="./assets/css/style2.css" />
    <title>Keranjang Belanja</title>
</head>

<body>
    <!-- navbar start -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow" aria-label="Offcanvas navbar large">
        <div class="container-fluid">
            <a class="navbar-brand">
                <img src="./assets/img/tokped.png" alt="" width="60" height="48" class="d-inline-block align-text-center" />
                <span class="textnav fw-bold text-success">TUNGPEDI</span>
            </a>

            <div class="offcanvas offcanvas-end text-bg-light" tabindex="-2" id="offcanvasNavbar2" aria-labelledby="offcanvasNavbar2Label">
                <div class="offcanvas-header">
                    <img src="./assets/img/logo.png" alt="" width="60" height="48" class="d-inline-block align-text-center" />
                    <h5 class="offcanvas-title off-nav" id="offcanvasNavbar2Label">Cosmetics</h5>
                    <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
            </div>
            <div class="ms-auto px-2">
                <a class="px-1" href=""><i class="iconnav" data-feather="search" id="search-button"></i></a>
                <a class="px-1" href=""><i class="iconnav" data-feather="shopping-cart" id="shopping-cart-button"></i></a>
                <a class="px-1" href="./login.html"><i class="iconnav" data-feather="user"></i></a>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar2" aria-controls="offcanvasNavbar2">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="container" style="margin-top: 6rem;">
        <h2>Keranjang Belanja</h2>
        <?php if (count($keranjang) > 0) : ?>
            <form action="checkout.php" method="post">
                <ul class="list-group mb-4">
                    <?php foreach ($keranjang as $index => $item) : ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <input type="radio" name="checkout_items[]" value="<?php echo $index; ?>" />
                                <input type="hidden" name="berat[<?php echo $index; ?>]" value="<?php echo $item['berat_produk']; ?>" />
                                <?php echo $item['nama_produk']; ?> - Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?>
                            </div>
                            <div class="d-flex align-items-center">
                                <a href="?action=kurang&id=<?php echo $item['id_produk']; ?>" class="btn btn-danger btn-sm mx-1">-</a>
                                <span><?php echo $item['quantity']; ?></span>
                                <a href="?action=tambah&id=<?php echo $item['id_produk']; ?>" class="btn btn-success btn-sm mx-1">+</a>
                                <a href="?action=hapus&id=<?php echo $item['id_produk']; ?>" class="btn btn-secondary btn-sm ml-2">Hapus</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <button type="submit" class="btn btn-primary btn-block">Checkout</button>
            </form>
        <?php else : ?>
            <p>Keranjang Anda kosong.</p>
        <?php endif; ?>
        <a href="index.php" class="btn btn-success mt-3">Lanjut Belanja</a>
    </div>
</body>

</html>