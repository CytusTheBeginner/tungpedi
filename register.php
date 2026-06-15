<?php
// Mulai session
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

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $user = $_POST['username'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $pass = $_POST['password'];
  $confirm_pass = $_POST['confirm_password'];
  $tanggal_lahir = $_POST['tanggal_lahir'];
  $jenis_kelamin = $_POST['jenis_kelamin'];
  $nama_jalan = $_POST['nama_jalan'];
  $kecamatan = $_POST['kecamatan'];
  $kabupaten = $_POST['kabupaten'];
  $kota = $_POST['kota'];
  $kode_pos = $_POST['kode_pos'];
  $provinsi = $_POST['provinsi'];

  // Gabungkan alamat
  $alamat = $nama_jalan . ', ' . $kecamatan . ', ' . $kabupaten . ', ' . $kota . ', ' . $kode_pos . ', ' . $provinsi;

  // Cek apakah password dan konfirmasi password sama
  if ($pass !== $confirm_pass) {
    echo "<script>alert('Password dan konfirmasi password tidak cocok');</script>";
  } else {
    // Cek apakah username atau email sudah digunakan di tabel users
    $sql = "SELECT * FROM tb_users WHERE username='$user' OR email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      echo "<script>alert('Username atau email sudah digunakan, silakan pilih username atau email lain');</script>";
    } else {
      // Hash password sebelum disimpan
      $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

      // Simpan data pengguna ke database dengan role default 'pembeli'
      $sql = "INSERT INTO tb_users (username, email, no_telp, password, role, nama, tanggal_lahir, jenis_kelamin, nama_jalan, kecamatan, kabupaten, kota, kode_pos, provinsi, alamat, created_at) 
              VALUES ('$user', '$email', '$phone', '$hashed_pass', 'pembeli', '$name', '$tanggal_lahir', '$jenis_kelamin', '$nama_jalan', '$kecamatan', '$kabupaten', '$kota', '$kode_pos', '$provinsi', '$alamat', NOW())";

      if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registrasi berhasil! Silakan login.');</script>";
        header("Location: login.php"); // Alihkan ke halaman login setelah registrasi
      } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
      }
    }
  }
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-image: url('assets/img/bg.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center center;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .card {
      background-color: rgba(255, 255, 255, 0.9);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
  </style>
  <title>Register Page</title>
</head>

<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header text-center">
            <h3 class="fw-bold">Tungpedi Customer Registration</h3>
          </div>
          <div class="card-body">
            <form method="POST" action="">
              <div class="mb-3">
                <label for="name" class="form-label fw-bold">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
              </div>
              <div class="mb-3">
                <label for="username" class="form-label fw-bold">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label fw-bold">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
              </div>
              <div class="mb-3">
                <label for="phone" class="form-label fw-bold">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label fw-bold">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
              </div>
              <div class="mb-3">
                <label for="confirm_password" class="form-label fw-bold">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
              </div>
              <div class="mb-3">
                <label for="tanggal_lahir" class="form-label fw-bold">Tanggal Lahir</label>
                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
              </div>
              <div class="mb-3">
                <label for="jenis_kelamin" class="form-label fw-bold">Jenis Kelamin</label>
                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                  <option value="Laki-Laki">Laki-laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="nama_jalan" class="form-label fw-bold">Nama Jalan</label>
                <input type="text" class="form-control" id="nama_jalan" name="nama_jalan" placeholder="Enter your street name" required>
              </div>
              <div class="mb-3">
                <label for="kecamatan" class="form-label fw-bold">Kecamatan</label>
                <input type="text" class="form-control" id="kecamatan" name="kecamatan" placeholder="Enter your sub-district" required>
              </div>
              <div class="mb-3">
                <label for="kabupaten" class="form-label fw-bold">Kabupaten</label>
                <input type="text" class="form-control" id="kabupaten" name="kabupaten" placeholder="Enter your regency" required>
              </div>
              <div class="mb-3">
                <label for="kota" class="form-label fw-bold">Kota</label>
                <input type="text" class="form-control" id="kota" name="kota" placeholder="Enter your city" required>
              </div>
              <div class="mb-3">
                <label for="kode_pos" class="form-label fw-bold">Kode Pos</label>
                <input type="text" class="form-control" id="kode_pos" name="kode_pos" placeholder="Enter your postal code" required>
              </div>
              <div class="mb-3">
                <label for="provinsi" class="form-label fw-bold">Provinsi</label>
                <input type="text" class="form-control" id="provinsi" name="provinsi" placeholder="Enter your province" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
          </div>
          <div class="card-footer text-center">
            <p class="fw-bold">&copy; 2024 TUNGPEDI, INC. All rights reserved.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>