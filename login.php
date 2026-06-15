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
  $user = $_POST['username'];
  $pass = $_POST['password'];

  // Mengamankan input dari SQL Injection
  $user = stripslashes($user);
  $user = $conn->real_escape_string($user);

  // Query untuk memeriksa username
  $sql = "SELECT * FROM tb_users WHERE username='$user'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Verifikasi password yang di-hash
    if (password_verify($pass, $row['password'])) {
      // Login berhasil, simpan informasi user ke session
      $_SESSION['username'] = $user;
      header("Location: index.php"); // Ganti dengan halaman dashboard kamu
    } else {
      // Password salah
      echo "<script>alert('Username atau password salah');
      window.location.href = 'login.php'</script>";
    }
  } else {
    // Username tidak ditemukan
    echo "<script>alert('Username atau password salah');
    window.location.href = 'login.php'</script>";
  }
}

// Tutup koneksi
$conn->close();
?>

<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS for background -->
  <style>
    body {
      background-image: url('assets/img/bg.jpg');
      /* Ganti dengan URL gambar latar belakang Anda */
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center center;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .card {
      background-color: rgba(255, 255, 255, 0.9);
      /* Transparansi latar belakang form */
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
  </style>

  <title>Login Page</title>
</head>

<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header text-center">
            <h3 class="fw-bold">Customer login</h3>
          </div>
          <div class="card-body">
            <form method="POST">
              <div class="mb-3">
                <label for="username" class="form-label fw-bold">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label fw-bold">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <span>Dont Have an Account? <a href="register.php" class="fw-bold">Register Here.</a></span>
          </div>
          <div class="card-footer text-center">
            <p class="fw-bold">&copy; 2024 TUNGPEDI, INC. All rights reserved.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>