<?php
session_start();

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'pedia_clone');

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil ID produk dari URL
$id_produk = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk mengambil data produk berdasarkan ID
$sql = "SELECT 
        tb_produk.*, 
        tb_users.nama AS nama_penjual, 
        tb_users.alamat AS alamat_penjual 
    FROM 
        tb_produk 
    JOIN 
        tb_users 
    ON 
        tb_produk.id_penjual = tb_users.id
    WHERE 
        tb_produk.id_produk = $id_produk";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data produk
    $row = $result->fetch_assoc();
} else {
    echo "<p>Produk tidak ditemukan.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    // Cek jika produk sudah ada di keranjang
    $item_exists = false;

    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    foreach ($_SESSION['keranjang'] as $index => $item) {
        if ($item['id_produk'] == $row['id_produk']) {
            $_SESSION['keranjang'][$index]['quantity'] += $quantity;
            $item_exists = true;
            break;
        }
    }

    if (!$item_exists) {
        $item = [
            'id_produk' => $row['id_produk'],
            'nama_produk' => $row['nama_produk'],
            'harga' => $row['harga'],
            'foto_produk' => $row['foto_produk'],
            'berat_produk' => $row['berat_produk'], // pastikan berat_produk ditambahkan ke keranjang
            'quantity' => $quantity
        ];
        $_SESSION['keranjang'][] = $item;
    }

    header('Location: keranjang.php');
    exit;
}
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
    <link rel="stylesheet" href="./assets/css/style.css" />
    <title>Detail Product</title>
    <style>
        .product-detail {
            display: flex;
            margin-top: 20px;
        }

        .product-image {
            width: 50%;
            text-align: center;
        }

        .product-image img {
            max-width: 100%;
            height: auto;
        }

        .product-info {
            width: 50%;
            padding: 20px;
        }

        .product-info h1 {
            font-size: 24px;
            font-weight: bold;
        }

        .product-info .price {
            font-size: 28px;
            color: green;
            font-weight: bold;
        }

        .product-info .original-price {
            text-decoration: line-through;
            color: #999;
        }

        .product-info .badge {
            font-size: 12px;
        }
    </style>
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

        <!-- search form -->
        <div class="kotak-search">
            <input type="search" id="search-box" placeholder="Search here..." />
        </div>
        <!-- search form -->
    </nav>
    <!-- navbar end -->
    <div class="container" style="margin-top: 6rem;">
        <a href="index.php" class="btn btn-danger">Kembali Ke Halaman Utama</a>
        <div class="product-detail">
            <div class="product-image">
                <img class="img-fluid" style="margin-top: 1rem;" src="images/<?php echo $row["foto_produk"]; ?>" alt="<?php echo $row["nama_produk"]; ?>">
            </div>
            <div class="product-info" style="margin-top: 1rem;">
                <h1><?php echo $row["nama_produk"]; ?></h1>
                <p class="price">Rp <?php echo number_format($row["harga"], 0, ',', '.'); ?></p>
                <h4 class="text-underline">Detail Penjual</h4>
                <p>Nama Penjual : <span class="fw-bold"><?= $row["nama_penjual"] ?></span></p>
                <p>Alamat Penjual : <span class="fw-bold"><?= $row["alamat_penjual"] ?></span></p>
                <hr>
                <h4>Detail Produk</h4>
                <p class="longtext-description" style="white-space: pre-wrap; font-size: 15px; color: #00000;"><?= $row["deksripsi"]; ?></p>
                <hr>
                <form action="detail_produk.php?id=<?php echo $row['id_produk']; ?>" method="POST">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <button class="btn btn-outline-secondary" type="button" onclick="decreaseQuantity()">-</button>
                        </div>
                        <input type="text" class="form-control" name="quantity" id="quantity" value="1" aria-label="Jumlah">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="increaseQuantity()">+</button>
                        </div>
                    </div>
                    <p>Sisa Stock : <span class="fw-bold"><?= $row['stok']; ?></span></p>
                    <?php if ($row["stok"] == 0): ?>
                        <p class="text-danger fw-bold mt-2">Stok habis. Tidak bisa menambahkan ke keranjang.</p>
                    <?php endif; ?>
                    <!-- Tombol Keranjang -->
                    <button type="submit" class="btn btn-success btn-block" <?php if ($row["stok"] == 0) echo 'disabled'; ?>>
                        + Keranjang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function increaseQuantity() {
            var quantityInput = document.getElementById('quantity');
            var currentQuantity = parseInt(quantityInput.value);
            if (currentQuantity < <?= $row['stok']; ?>) {
                quantityInput.value = currentQuantity + 1;
            }
        }

        function decreaseQuantity() {
            var quantityInput = document.getElementById('quantity');
            var currentQuantity = parseInt(quantityInput.value);
            if (currentQuantity > 1) {
                quantityInput.value = currentQuantity - 1;
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>