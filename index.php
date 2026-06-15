<?php
session_start();
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
  <title>Tungpedi</title>
  <style>

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
        <div class="offcanvas-body">
          <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
            <li class="nav-item">
              <a class="nav-link" aria-current="page" href="">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#product">Product</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#contact">Our Contacts</a>
            </li>
          </ul>
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

  <!-- banner -->
  <div id="carouselExampleInterval" class="container carousel slide mt-5 pt-5 pb-4" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active" data-bs-interval="1200">
        <img src="https://images.tokopedia.net/img/cache/1208/NsjrJu/2024/7/31/cf3c9fd8-e989-4fcc-859e-12100fc9f654.jpg.webp?ect=4g" class="d-block w-100 rounded" alt="Banner Iklan" />
      </div>
      <div class="carousel-item" data-bs-interval="1200">
        <img src="https://images.tokopedia.net/img/cache/1208/NsjrJu/2024/7/31/cf3c9fd8-e989-4fcc-859e-12100fc9f654.jpg.webp?ect=4g" class="d-block w-100 rounded" alt="Banner Iklan" />
      </div>
    </div>
  </div>
  <!-- banner end -->

  <!-- product -->
  <section id="product" class="pt-5 pb-5">
    <div class="container">
      <div class="title text-center">
        <h2 class="position-relative d-inline-block pt-5">
          <span class="star-color"><i class="bi bi-star-fill"></i></span> Our Best Products <span class="star-color"><i class="bi bi-star-fill"></i></span>
        </h2>
      </div>
      <div class="row">
        <?php
        // Koneksi ke database
        $conn = new mysqli('localhost', 'root', '', 'pedia_clone');

        // Periksa koneksi
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }

        // Query untuk mengambil data produk
        $sql = "SELECT id_produk, nama_produk, harga, foto_produk FROM tb_produk";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          // Output data setiap baris
          while ($row = $result->fetch_assoc()) {
            echo '
                      <div class="col-md-3 pt-3">
                          <div class="card mb-4">
                              <a href="detail_produk.php?id=' . $row["id_produk"] . '">
                                  <img src="images/' . $row["foto_produk"] . '" class="card-img-top img-fluid" alt="' . $row["nama_produk"] . '">
                              </a>
                              <div class="card-body">
                                  <h5 class="card-title">
                                      <a href="detail_produk.php?id=' . $row["id_produk"] . '" style="text-decoration: none; color: black;">' . $row["nama_produk"] . '</a>
                                  </h5>
                                  <p class="card-text">Rp ' . number_format($row["harga"], 0, ',', '.') . '</p>
                              </div>
                          </div>
                      </div>';
          }
        } else {
          echo "No Products Found";
        }
        $conn->close();
        ?>
      </div>
    </div>
  </section>
  <!-- Best Seller end-->

  <!-- Contact Us -->
  <section class="pt-5 pb-5 c-us" id="contact">
    <div class="container">
      <div class="row px-3">
        <h3 class="text-center pb-3 pt-5">Our Contacts</h3>
        <div class="col-md-12 pt-5">
          <div class="row mb-3">
            <div class="col-6">
              <div class="border rounded p-3 text-center bg-cs shadow">
                <h6 class="mb-3 fw-bold">Phone Number</h6>
                <p class="fs-cs">+6281213141516</p>
              </div>
            </div>
            <div class="col-6">
              <div class="border rounded p-3 text-center bg-cs shadow">
                <h6 class="mb-3 fw-bold">Email Address</h6>
                <p class="fs-cs">tungpedi.inc@gmail.com</p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="border rounded p-3 text-center bg-cs shadow">
                <h6 class="mb-3 fw-bold">Office Address</h6>
                <p class="fs-cs">Ketapang Muda Street No. 130<br />City : Tanjungpinang<br />Province : Kepulauan
                  Riau<br />Zip Code : 29125</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Contact Us End -->

  <!-- footer -->
  <footer class="d-flex justify-content-center footer bg-dark text-light">
    <div class="container align-items-center">
      <div class="row">
        <div class="col-md-3 mx-auto py-2">
          <div class="kirifoot">
            <img src="./assets/img/tokped.png" alt="" width="60" height="48" class="d-inline-block align-text-center" />
            <span class="textnav">Tungpedi</span>
          </div>
          <div>
            <p>The Most High Demand E-Commerce in 2100 Century</p>
          </div>
          <div>
            <a href=""><i data-feather="facebook" class="mx-1 social-logo"></i></a>
            <a href=""><i data-feather="instagram" class="mx-1 social-logo"></i></a>
            <a href=""><i data-feather="twitter" class="mx-1 social-logo"></i></a>
            <br />
          </div>
        </div>
        <div class="col-md-3 mx-auto pt-3">
          <div>
            <p class="fw-bold">Payment</p>
          </div>
          <div>
            <p>We Accept Following Payment</p>
            <img src="./assets/img/bca-logo.png" class="pt-2" alt="" />
            <img src="./assets/img/bni-logo.png" class="pt-2" alt="" />
            <img src="./assets/img/gopay.png" class="pt-2" alt="" />
          </div>
        </div>
        <div class="col-md-3 mx-auto pt-3">
          <div>
            <p class="fw-bold">Contact Info</p>
          </div>
          <div>
            <p>Ketapang Muda Street No.130, Tanjungpinang City, Kepulauan Riau Province.</p>
          </div>
          <div class="textcontactsfoot">
            <p>Phone : +6281213141516</p>
            <p>Email : cosmetics@gmail.com</p>
            <p>Zip Code : 29125</p>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <!-- footer end -->

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  <script>
    feather.replace();
  </script>
  <script src="./assets/js/script.js"></script>
</body>

</html>