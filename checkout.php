<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Buat koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'pedia_clone');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data pengguna berdasarkan username dari session
$username = $_SESSION['username'];
$sql_user = "SELECT id, nama, alamat, no_telp, email FROM tb_users WHERE username = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $nama = $user['nama'];
    $id_pembeli = $user['id'];
    $alamat = $user['alamat'];
    $telepon = $user['no_telp'];
    $email = $user['email'];
} else {
    echo "<script>alert('Data pengguna tidak ditemukan. Silakan login ulang.');
    window.location.href = 'login.php';</script>";
    exit;
}

$stmt_user->close();

// Ambil keranjang dari session
$keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['checkout_items']) || empty($_POST['checkout_items'])) {
        echo "<script>alert('Tidak ada barang yang dipilih untuk checkout.');
        window.location.href = 'keranjang.php';</script>";
        exit;
    }

    $selected_items = $_POST['checkout_items'];
    $selected_products = [];
    $total_harga = 0;
    $total_berat = 0;

    foreach ($selected_items as $index) {
        if (isset($keranjang[$index])) {
            $selected_products[] = $keranjang[$index];
            $total_harga += $keranjang[$index]['harga'] * $keranjang[$index]['quantity'];
            $total_berat += $keranjang[$index]['berat_produk'] * $keranjang[$index]['quantity'];
        }
    }

    if (isset($_POST['confirm'])) {
        // Biaya tambahan
        $biaya_layanan = 1000;
        $biaya_jasa_aplikasi = 1000;
        $biaya_asuransi = $total_harga * 0.01;
        $id_metode = $_POST['metode_pembayaran'];

        // Tentukan Ongkos Kirim berdasarkan total berat
        $id_jasa_kirim = 1;
        if ($total_berat > 100 && $total_berat <= 500) {
            $id_jasa_kirim = 2;
        } elseif ($total_berat > 500 && $total_berat <= 1000) {
            $id_jasa_kirim = 3;
        } elseif ($total_berat > 1000) {
            $id_jasa_kirim = 4;
        }

        $sql_jasa = "SELECT biaya FROM tb_jasa_kirim WHERE id_jasa = $id_jasa_kirim";
        $result_jasa = $conn->query($sql_jasa);
        $ongkos_kirim = 0;
        if ($result_jasa->num_rows > 0) {
            $row_jasa = $result_jasa->fetch_assoc();
            $ongkos_kirim = $row_jasa['biaya'];
        }

        $total_akhir = $total_harga + $biaya_layanan + $biaya_jasa_aplikasi + $biaya_asuransi + $ongkos_kirim;

        // Mengambil no_pesanan terakhir
        $result = $conn->query("SELECT no_pesanan FROM tb_pesanan ORDER BY no_pesanan DESC LIMIT 1");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last_no_pesanan = intval(substr($row['no_pesanan'], -4)); // Ambil nomor urut terakhir
        } else {
            $last_no_pesanan = 0; // Jika belum ada pesanan
        }
        $new_no_pesanan = $last_no_pesanan + 1;
        $no_pesanan =  date('Ymd') . '/'  . 'MPL/' .  str_pad($new_no_pesanan, 4, '0', STR_PAD_LEFT);

        $stmt = $conn->prepare("INSERT INTO tb_pesanan (id_pembeli, no_pesanan, nama_penerima, alamat, telepon, total_harga, biaya_layanan, biaya_jasa_aplikasi, biaya_asuransi, id_metode, id_jasa, total_akhir, total_berat) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssisiiiiiii", $id_pembeli, $no_pesanan, $nama, $alamat, $telepon, $total_harga, $biaya_layanan, $biaya_jasa_aplikasi, $biaya_asuransi, $id_metode, $id_jasa_kirim, $total_akhir, $total_berat);

        if ($stmt->execute()) {
            $id_pesanan = $stmt->insert_id;
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO tb_detail_pesanan (id_pesanan, id_produk, harga, quantity) VALUES (?, ?, ?, ?)");
            foreach ($selected_products as $item) {
                $stmt->bind_param("iidi", $id_pesanan, $item['id_produk'], $item['harga'], $item['quantity']);
                $stmt->execute();
            }
            $stmt->close();

            $_SESSION['keranjang'] = [];

            header("Location: invoice.php?id_pesanan=$id_pesanan");
            exit;
        } else {
            echo "<script>alert('Gagal Melakukan Checkout. Silakan coba lagi.');
            window.location.href = 'keranjang.php';</script>";
        }

        $conn->close();
    } else {
        // Menampilkan form konfirmasi
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
            <title>Konfirmasi Pesanan</title>
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
            <!-- navbar end -->
            <div class="container" style="margin-top: 6rem;">
                <a href="index.php" class="btn btn-danger mb-4">Kembali Ke Halaman Utama</a>
                <h2>Konfirmasi Pesanan</h2>
                <ul class="list-group mb-4">
                    <?php foreach ($selected_products as $item) : ?>
                        <li class="list-group-item">
                            <?php echo $item['nama_produk']; ?> - Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?> (Jumlah: <?php echo $item['quantity']; ?>)
                        </li>
                    <?php endforeach; ?>
                    <li class="list-group-item font-weight-bold">Total: Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></li>
                </ul>

                <form action="checkout.php" method="POST">
                    <input type="hidden" name="confirm" value="true">
                    <input type="hidden" id="id_user" value="<?= $id_pembeli ?>">
                    <div class="mb-3 form-group">
                        <label for="nama">Nama Pembeli:</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= $nama; ?>" required disabled>
                        <small>Nama Pembeli disesuaikan dari Nama Pengguna</small>
                    </div>
                    <div class="mb-3 form-group">
                        <label for="alamat">Alamat:</label>
                        <textarea class="form-control" id="alamat" name="alamat" required disabled><?= $alamat; ?></textarea>
                        <small>Untuk Mengubah Alamat Silahkan Ubah di Profile</small>
                    </div>
                    <div class="mb-3 form-group">
                        <label for="telepon">Telepon:</label>
                        <input type="number" class="form-control" id="telepon" name="telepon" value="<?= $telepon; ?>" required disabled>
                    </div>
                    <div class="mb-3 form-group">
                        <label for="metode_pembayaran">Metode Pembayaran:</label>
                        <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                            <option value="1">BCA Virtual Account</option>
                            <option value="2">GoPay</option>
                            <option value="3">QRIS</option>
                            <option value="4">OVO</option>
                        </select>
                    </div>
                    <?php foreach ($selected_items as $index) : ?>
                        <input type="hidden" name="checkout_items[]" value="<?php echo $index; ?>">
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-primary btn-block">Konfirmasi Pesanan</button>
                </form>
            </div>
        </body>

        </html>
<?php
    }
} else {
    echo "<script>alert('Tidak ada barang dikeranjang anda.');
        window.location.href = 'keranjang.php';</script>";
}
?>