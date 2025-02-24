<?php
$title = "Form Pengaduan";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../includes/db.php';

    // Ambil data dari form
    $nama_pelapor = trim($_POST['nama_pelapor']);
    $no_wa = trim($_POST['no_wa']);
    $email_pelapor = trim($_POST['email_pelapor']);
    $role_pelapor = trim($_POST['role_pelapor']);
    $kategori = trim($_POST['kategori']);
    $judul_pengaduan = trim($_POST['judul_pengaduan']);
    $isi_pengaduan = trim($_POST['isi_pengaduan']);
    $keterangan = trim($_POST['keterangan']);

    // Upload file pendukung
    $file_pendukung = '';
    if (isset($_FILES['file_pendukung']) && $_FILES['file_pendukung']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = basename($_FILES['file_pendukung']['name']);
        $file_path = $upload_dir . $file_name;
        move_uploaded_file($_FILES['file_pendukung']['tmp_name'], $file_path);
        $file_pendukung = $file_name;
    }

    if (!empty($nama_pelapor) && !empty($role_pelapor) && !empty($kategori) && !empty($judul_pengaduan) && !empty($isi_pengaduan)) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO Pengaduan (nama_pelapor, no_wa, email_pelapor, role_pelapor, kategori, judul_pengaduan, isi_pengaduan, keterangan, file_pendukung)
                VALUES (:nama_pelapor, :no_wa, :email_pelapor, :role_pelapor, :kategori, :judul_pengaduan, :isi_pengaduan, :keterangan, :file_pendukung)
            ");
            $stmt->bindParam(':nama_pelapor', $nama_pelapor);
            $stmt->bindParam(':no_wa', $no_wa);
            $stmt->bindParam(':email_pelapor', $email_pelapor);
            $stmt->bindParam(':role_pelapor', $role_pelapor);
            $stmt->bindParam(':kategori', $kategori);
            $stmt->bindParam(':judul_pengaduan', $judul_pengaduan);
            $stmt->bindParam(':isi_pengaduan', $isi_pengaduan);
            $stmt->bindParam(':keterangan', $keterangan);
            $stmt->bindParam(':file_pendukung', $file_pendukung);
            $stmt->execute();
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Pengaduan Berhasil Dikirim!',
                        text: 'Terima kasih telah mengirimkan pengaduan.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                  </script>";
        } catch (\PDOException $e) {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: '" . htmlspecialchars($e->getMessage()) . "',
                        confirmButtonText: 'OK'
                    });
                  </script>";
        }
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Semua kolom wajib diisi!',
                    confirmButtonText: 'OK'
                });
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: rgb(101, 106, 250);
        }
        .header {
            background-image: url('../assets/gbanner.jpg'); /* Ganti dengan path gambar Anda */
            background-size: cover;
            background-position: center;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .form-container {
            max-width: 600px;
            margin: -50px auto 50px auto; /* Overlap header */
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body>
    <!-- Header with Image -->
    <div class="header">
        <h1>Sistem Layanan Pengaduan Sekolah</h1>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <h2 class="text-primary text-center py-2">Form Layanan Pengaduan Salassika</h2>
        <hr>
        <form method="POST" action="" enctype="multipart/form-data" id="pengaduanForm">
            <div class="mb-3">
                <label for="nama_pelapor" class="form-label">Nama Pelapor</label>
                <input type="text" class="form-control" id="nama_pelapor" name="nama_pelapor" required>
            </div>
            <div class="mb-3">
                <label for="no_wa" class="form-label">Nomor WhatsApp</label>
                <input type="text" class="form-control" id="no_wa" name="no_wa" placeholder="Contoh: 081234567890">
            </div>
            <div class="mb-3">
                <label for="email_pelapor" class="form-label">Email Pelapor</label>
                <input type="email" class="form-control" id="email_pelapor" name="email_pelapor">
            </div>
            <div class="mb-3">
                <label for="role_pelapor" class="form-label">Peran Anda</label>
                <select class="form-select" id="role_pelapor" name="role_pelapor" required>
                    <option value="siswa">Siswa</option>
                    <option value="guru">Guru</option>
                    <option value="umum">Umum</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <select class="form-select" id="kategori" name="kategori" required>
                    <option value="saran">Saran</option>
                    <option value="kritik">Kritik</option>
                    <option value="pembelajaran">Pembelajaran</option>
                    <option value="organisasi">Organisasi</option>
                    <option value="administrasi">Administrasi</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="judul_pengaduan" class="form-label">Judul Pengaduan</label>
                <input type="text" class="form-control" id="judul_pengaduan" name="judul_pengaduan" required>
            </div>
            <div class="mb-3">
                <label for="isi_pengaduan" class="form-label">Pesan</label>
                <textarea class="form-control" id="isi_pengaduan" name="isi_pengaduan" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan Tambahan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="file_pendukung" class="form-label">Upload File Pendukung (Opsional)</label>
                <input type="file" class="form-control" id="file_pendukung" name="file_pendukung">
            </div>
            <button type="submit" class="btn btn-primary w-100">Kirim Pengaduan</button>
        </form>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Validasi form interaktif
        document.getElementById('pengaduanForm').addEventListener('submit', function (e) {
            const namaPelapor = document.getElementById('nama_pelapor').value.trim();
            const rolePelapor = document.getElementById('role_pelapor').value.trim();
            const kategori = document.getElementById('kategori').value.trim();
            const judulPengaduan = document.getElementById('judul_pengaduan').value.trim();
            const isiPengaduan = document.getElementById('isi_pengaduan').value.trim();

            if (!namaPelapor || !rolePelapor || !kategori || !judulPengaduan || !isiPengaduan) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Semua kolom wajib diisi!',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
</body>
</html>