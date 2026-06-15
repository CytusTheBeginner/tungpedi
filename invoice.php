<?php
session_start();
// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require('./vendor/setasign/fpdf/fpdf.php');

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'pedia_clone');

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil ID pesanan dari URL
$id_pesanan = isset($_GET['id_pesanan']) ? intval($_GET['id_pesanan']) : 0;

// Query untuk mengambil data pesanan
$sql = "SELECT 
    tb_pesanan.*, 
    tb_jasa_kirim.*,
    tb_detail_pesanan.*, 
    tb_produk.nama_produk, 
    tb_produk.berat_produk, 
    penjual.nama AS nama_penjual,
    pembeli.nama AS nama_pembeli,
    tb_metode_bayar.nama_metode
FROM 
    tb_pesanan 
JOIN 
    tb_jasa_kirim ON tb_pesanan.id_jasa = tb_jasa_kirim.id_jasa
JOIN 
    tb_detail_pesanan ON tb_pesanan.id_pesanan = tb_detail_pesanan.id_pesanan
JOIN 
    tb_produk ON tb_detail_pesanan.id_produk = tb_produk.id_produk
JOIN 
    tb_users AS penjual ON tb_produk.id_penjual = penjual.id
JOIN 
    tb_users AS pembeli ON tb_pesanan.id_pembeli = pembeli.id
JOIN 
    tb_metode_bayar ON tb_pesanan.id_metode = tb_metode_bayar.id_metode
WHERE 
    tb_pesanan.id_pesanan = $id_pesanan
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $pesanan = $result->fetch_assoc();
} else {
    echo "<p>Pesanan tidak ditemukan.</p>";
    exit;
}

// Query untuk mengambil detail pesanan
$sql = "SELECT tb_detail_pesanan.*, tb_produk.nama_produk 
        FROM tb_detail_pesanan 
        JOIN tb_produk ON tb_detail_pesanan.id_produk = tb_produk.id_produk 
        WHERE id_pesanan = $id_pesanan";
$result = $conn->query($sql);

$detail_pesanan = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $detail_pesanan[] = $row;
    }
} else {
    echo "<p>Detail pesanan tidak ditemukan.</p>";
    exit;
}

$conn->close();

// Membuat PDF Invoice
class PDF extends FPDF
{
    private $pesanan;

    public function __construct($pesanan, $orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
        $this->pesanan = $pesanan;
    }

    function Header()
    {
        $this->SetFont('Arial', 'B', 30);
        $this->SetTextColor(0, 128, 0);
        $this->Cell(100, 20, 'TUNGPEDI', 0, 0, 'L');

        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 10, 'INVOICE', 0, 1, 'R');

        $this->SetFont('Arial', '', 10);
        $this->SetX(200);
        $this->Cell(0, 10, 'INV/' . $this->pesanan['no_pesanan'], 0, 1, 'R');
        $this->SetTextColor(0, 0, 0);
        $this->Ln(10);
    }
}

$pdf = new PDF($pesanan); // Meneruskan data pesanan saat membuat objek PDF
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Seller Information
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(90, 6, 'DITERBITKAN ATAS NAMA', 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'UNTUK', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 6, 'Penjual', 0, 0);
$pdf->Cell(60, 6, ':  ' . $pesanan['nama_penjual'], 0, 0);
$pdf->Cell(35, 6, 'Pembeli', 0, 0);
$pdf->Cell(0, 6, ':  ' . $pesanan['nama_pembeli'], 0, 1);

$pdf->Cell(30, 6, 'Pre-order', 0, 0);
$pdf->Cell(60, 6, ':  -', 0, 0);
$pdf->Cell(35, 7, 'Tanggal Pembelian', 0, 0);
$pdf->Cell(0, 6, ':  ' . date('d M Y', strtotime($pesanan['tanggal_pesanan'])), 0, 1);

$pdf->Cell(30, 6, '', 0, 0); // Empty cell for alignment
$pdf->Cell(60, 6, '', 0, 0); // Empty cell for alignment
$pdf->Cell(35, 7, 'Alamat Pengiriman', 0, 0);
$pdf->MultiCell(0, 6, ":  " . $pesanan['nama_penerima'] . " (" . $pesanan['telepon'] . ")\n" . $pesanan['alamat'] . ".", 0, 1);
$pdf->Ln(8);

// Pricing Info
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(100, 15, 'INFO PRODUK', 'TB', 0);
$pdf->Cell(20, 15, 'JUMLAH', 'TB', 0, 'C');
$pdf->Cell(35, 15, 'HARGA SATUAN', 'TB', 0, 'C');
$pdf->Cell(35, 15, 'TOTAL HARGA', 'TB', 0, 'R');
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
foreach ($detail_pesanan as $item) {
    $pdf->MultiCell(100, 8, $item['nama_produk'] . "\nBerat: " . $pesanan['total_berat'] . "g", 0);
    $pdf->SetXY($pdf->GetX() + 100, $pdf->GetY() - 12); // Adjust Y position after MultiCell
    $pdf->Cell(20, 8, $item['quantity'], 0, 0, 'C'); // Menampilkan kuantitas
    $pdf->Cell(35, 8, 'Rp ' . number_format($item['harga'], 0, ',', '.'), 0, 0, 'C');
    $pdf->Cell(35, 8, 'Rp ' . number_format($item['harga'] * $item['quantity'], 0, ',', '.'), 0, 0, 'R');
    $pdf->Ln(18);
}

// Total
$total_quantity = array_sum(array_column($detail_pesanan, 'quantity'));
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetX(115);
$pdf->Cell(50, 6, 'TOTAL HARGA (' . $total_quantity . ' BARANG)', 0, 0);
$pdf->Cell(35, 6, 'Rp ' . number_format($pesanan['total_harga'], 0, ',', '.'), 0, 1, 'R');
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 10);
$pdf->SetX(115);
$pdf->Cell(50, 6, 'Total Ongkos Kirim', 0, 0);
$pdf->Cell(35, 6, 'Rp ' . number_format($pesanan['biaya'], 0, ',', '.'), 0, 1, 'R');
$pdf->Ln(2);
$pdf->SetX(115);
$pdf->Cell(50, 6, 'Biaya Asuransi Pengiriman', 0, 0);
$pdf->Cell(35, 6, 'Rp ' . number_format($pesanan['biaya_asuransi'], 0, ',', '.'), 0, 1, 'R');
$pdf->Ln(2);
$pdf->SetX(115);
$total_bl = $pesanan['total_harga'] + $pesanan['biaya'] + $pesanan['biaya_asuransi'];
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(50, 6, 'TOTAL BELANJA', 0, 0);
$pdf->Cell(35, 6, 'Rp ' . number_format($total_bl, 0, ',', '.'), 0, 1, 'R');
$pdf->Ln(2);
$pdf->SetX(115);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 6, 'Biaya Layanan', 0, 0);
$pdf->Cell(35, 6, 'Rp 1.000', 0, 1, 'R');
$pdf->Ln(2);
$pdf->SetX(115);
$pdf->Cell(50, 6, 'Biaya Jasa Aplikasi', 0, 0);
$pdf->Cell(35, 6, 'Rp 1.000', 0, 1, 'R');
$pdf->Ln(2);
$pdf->SetX(115);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(50, 6, 'TOTAL TAGIHAN', 0, 0);
$pdf->Cell(35, 6, 'Rp ' . number_format($pesanan['total_akhir'], 0, ',', '.'), 0, 1, 'R');
$pdf->Ln(10);

// informasi kirim
$pdf->SetFont('Arial', '', 10);
// Kolom Kiri
$pdf->Cell(105, 8, 'Kurir:', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 8, 'Metode Pembayaran:', 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(105, 8, $pesanan['nama_kurir'], 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 8, $pesanan['nama_metode'], 0, 1, 'L');

$pdf->Cell(105, 8, 'Asuransi Pengiriman Tungpedi', 0, 0, 'L');
$pdf->Cell(95, 8, '', 0, 1, 'L');

$pdf->Cell(105, 8, '', 0, 0, 'L'); // Kosong untuk merapikan baris
$pdf->Cell(95, 8, '', 0, 1, 'L');
$pdf->Ln(3);

// Footer note
$pdf->SetFont('Arial', 'I', 9);
$pdf->Cell(105, 6, 'Invoice ini sah dan diproses oleh komputer', 0, 1);
$pdf->Cell(105, 6, 'Silakan hubungi Tungpedi Care apabila kamu membutuhkan bantuan.', 0, 1);
$pdf->Cell(105, 6, '', 0, 0, 'L');
$pdf->Cell(85, 6, 'Terakhir diupdate: ' . date('d M Y H:i:s', strtotime($pesanan['tanggal_pesanan'])), 0, 1, 'R');

$pdf->Output('I', 'Invoice_' . $pesanan['no_pesanan'] . '.pdf');
