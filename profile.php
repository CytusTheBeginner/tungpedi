<?php
session_start();

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pedia_clone"; // Ganti dengan nama database kamu

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Ambil data pengguna dari database
$sql = "SELECT * FROM tb_users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user_data = $result->fetch_assoc();
} else {
    echo "Pengguna tidak ditemukan!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $field = $_POST['field'];
    $value = $_POST['value'];

    // Update field yang diubah
    $sql_update = "UPDATE tb_users SET $field='$value' WHERE username='$username'";
    if ($conn->query($sql_update) === TRUE) {

        // Ambil data terbaru setelah update field
        $sql = "SELECT * FROM tb_users WHERE username='$username'";
        $result = $conn->query($sql);
        $user_data = $result->fetch_assoc();

        // Jika salah satu dari nama jalan, kecamatan, kabupaten, kota, kodepos, atau provinsi diubah, perbarui alamat lengkap
        if (in_array($field, ['nama_jalan', 'kecamatan', 'kabupaten', 'kota', 'kode_pos', 'provinsi'])) {
            // Gabungkan alamat lengkap baru
            $alamat_lengkap = $user_data['nama_jalan'] . ', ' . $user_data['kecamatan'] . ', ' . $user_data['kabupaten'] . ', ' . $user_data['kota'] . ', ' . $user_data['kode_pos'] . ', ' . $user_data['provinsi'];

            // Update alamat lengkap
            $sql_update_alamat = "UPDATE tb_users SET alamat='$alamat_lengkap' WHERE username='$username'";
            $conn->query($sql_update_alamat);
        }

        echo "<script>alert('Data berhasil diperbarui!');
        window.location.href = 'profile.php'</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!');
        window.location.href = 'profile.php'</script>";
    }
}

// Tutup koneksi
$conn->close();
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
    <title>Profile</title>
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
                <a class="px-1" href="keranjang.php"><i class="iconnav" data-feather="shopping-cart" id="shopping-cart-button"></i></a>
                <a class="px-1" href="<?php echo isset($_SESSION['username']) ? 'profile.php' : 'login.php'; ?>">
                    <i class="iconnav" data-feather="user"></i>
                </a>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar2" aria-controls="offcanvasNavbar2">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
    <!-- navbar end -->

    <div class="container" style="margin-top: 6rem;">
        <a href="index.php" class="btn btn-danger">Kembali Ke Halaman Utama</a>
        <h2 class="text-center mt-1">Profile Pengguna</h2>
        <div class="card mt-5">
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Nama Lengkap</th>
                        <td><?php echo $user_data['nama']; ?></td>
                        <td><button class="btn btn-success" onclick="showUpdateModal('nama', '<?php echo $user_data['nama']; ?>', 'text')">Ubah</button></td>
                    </tr>
                    <tr>
                        <th>No Telp</th>
                        <td><?php echo $user_data['no_telp']; ?></td>
                        <td><button class="btn btn-success" onclick="showUpdateModal('no_telp', '<?php echo $user_data['no_telp']; ?>', 'number')">Ubah</button></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo $user_data['email']; ?></td>
                        <td><button class="btn btn-success" onclick="showUpdateModal('email', '<?php echo $user_data['email']; ?>', 'email')">Ubah</button></td>
                    </tr>
                    <tr>
                        <th>Tanggal Lahir</th>
                        <td><?php echo $user_data['tanggal_lahir']; ?></td>
                        <td><button class="btn btn-success" onclick="showUpdateModal('tanggal_lahir', '<?php echo $user_data['tanggal_lahir']; ?>', 'date')">Ubah</button></td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <td><?php echo $user_data['jenis_kelamin'] == 'Laki-Laki' ? 'Laki-laki' : 'Perempuan'; ?></td>
                        <td><button class="btn btn-success" onclick="showUpdateModal('jenis_kelamin', '<?php echo $user_data['jenis_kelamin']; ?>', 'select')">Ubah</button></td>
                    </tr>
                    <tr>
                        <th>Nama Jalan</th>
                        <td><?php echo $user_data['nama_jalan']; ?></td>
                        <td><button class="btn btn-success" onclick="showUpdateModal('nama_jalan', '<?php echo $user_data['nama_jalan']; ?>', 'text')">Ubah</button></td>
                    </tr>
                    <tr>
                        <th>Kecamatan</th>
                        <td><?php echo $user_data['kecamatan']; ?></td>
                        <td><button class="btn btn-success" onclick="showUpdateModal('kecamatan', '<?php echo $user_data['kecamatan']; ?>', 'text')">Ubah</button></td>
                    </tr>
                    <tr>
                        <th>Kabupaten</th>
                        <td><?php echo $user_data['kabupaten']; ?></td>
                        <td><button class="btn btn-success" onclick="showUpdateModal('kabupaten', '<?php echo $user_data['kabupaten']; ?>', 'text')">Ubah</button></td>
                    </tr>
                    <tr>
                        <th>Kota</th>
                        <td><?php echo $user_data['kota']; ?></td>
                        <td><button class="btn btn-success" onclick="showUpdateModal('kota', '<?php echo $user_data['kota']; ?>', 'text')">Ubah</button></td>
                    </tr>
                    <tr>
                        <th>Kode Pos</th>
                        <td><?php echo $user_data['kode_pos']; ?></td>
                        <td><button class="btn btn-success" onclick="showUpdateModal('kode_pos', '<?php echo $user_data['kode_pos']; ?>', 'number')">Ubah</button></td>
                    </tr>
                    <tr>
                        <th>Provinsi</th>
                        <td><?php echo $user_data['provinsi']; ?></td>
                        <td><button class="btn btn-success" onclick="showUpdateModal('provinsi', '<?php echo $user_data['provinsi']; ?>', 'text')">Ubah</button></td>
                    </tr>
                    <tr>
                        <th>Alamat Lengkap</th>
                        <td><?php echo $user_data['alamat']; ?></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Modal untuk update -->
        <div class="modal fade" id="updateModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Ubah Data</h5>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <input type="hidden" id="field" name="field">
                            <div class="mb-3" id="inputContainer">
                                <label for="value" id="fieldLabel" class="form-label"></label>
                                <input type="text" class="form-control" id="value" name="value" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 mb-4 col-md-3" style="float:right;">
            <?php if ($user_data['role'] == 'penjual'): ?>
                <button class="btn btn-primary">Manage Produk</button>
            <?php else: ?>
                <button class="btn btn-success">Ajukan Menjadi Penjual</button>
            <?php endif; ?>
            <a class="btn btn-danger" href="logout.php">Log Out</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var updateModal = document.getElementById('updateModal');

            updateModal.addEventListener('hidden.bs.modal', function() {
                // Reset input field setiap kali modal ditutup
                document.getElementById('field').value = '';
                document.getElementById('inputContainer').innerHTML = '';
            });
        });

        function showUpdateModal(field, currentValue, type) {
            document.getElementById('field').value = field;
            document.getElementById('fieldLabel').innerText = field.replace('_', ' ').toUpperCase();

            var inputContainer = document.getElementById('inputContainer');
            inputContainer.innerHTML = ''; // Bersihkan container input sebelum mengisi yang baru

            if (type === 'select') {
                var select = document.createElement('select');
                select.className = 'form-select';
                select.id = 'value';
                select.name = 'value';

                var optionL = document.createElement('option');
                optionL.value = 'Laki-Laki';
                optionL.text = 'Laki-laki';
                if (currentValue === 'Laki-Laki') optionL.selected = true;
                select.appendChild(optionL);

                var optionP = document.createElement('option');
                optionP.value = 'Perempuan';
                optionP.text = 'Perempuan';
                if (currentValue === 'Perempuan') optionP.selected = true;
                select.appendChild(optionP);

                inputContainer.appendChild(select);
            } else {
                var input = document.createElement('input');
                input.type = type;
                input.className = 'form-control';
                input.id = 'value';
                input.name = 'value';
                input.value = currentValue;
                input.required = true;
                inputContainer.appendChild(input);
            }

            var updateModalInstance = new bootstrap.Modal(document.getElementById('updateModal'));
            updateModalInstance.show();
        }
    </script>
</body>

</html>