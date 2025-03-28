# Absensi Sekolah

<img src="https://www.logotypes101.com/logos/203/272663FA02DE2DAA2BBAE2FC39F14783/php.png" alt="PHP Logo" width="200">

Proyek ini adalah sistem absensi sekolah yang dibangun menggunakan PHP dan MySQL. Sistem ini memungkinkan sekolah untuk melacak kehadiran siswa secara efisien.

## Fitur

- **Manajemen Siswa**: Tambah, edit, dan hapus data siswa.
- **Manajemen Guru**: Tambah, edit, dan hapus data guru.
- **Manajemen Kelas**: Tambah, edit, dan hapus data kelas.
- **Absensi**: Catat kehadiran siswa setiap hari.
- **Laporan**: Lihat laporan kehadiran siswa.
- **Login Multi-User**: Masuk sebagai admin atau guru.

## Persyaratan

- XAMPP atau server web dengan PHP dan MySQL
- Browser web modern

## Instalasi

1. Clone repositori ini ke direktori htdocs XAMPP Anda:
    ```bash
    git clone https://github.com/username/absensi_sekolah.git
    ```

2. Buat database baru di MySQL dan impor file `database.sql` yang ada di folder `database`:
    ```sql
    CREATE DATABASE absensi_sekolah;
    USE absensi_sekolah;
    SOURCE path/to/database.sql;
    ```

3. Konfigurasi koneksi database di file `config.php`:
    ```php
    // filepath: config.php
    <?php
    $servername = getenv('DB_SERVER') ?: 'localhost';
    $username = getenv('DB_USERNAME') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';
    $dbname = getenv('DB_NAME') ?: 'absensi_sekolah';

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>
    ```

4. Buat file `.env` di direktori root proyek dan tambahkan konfigurasi database:
    ```env
    DB_SERVER=localhost
    DB_USERNAME=root
    DB_PASSWORD=
    DB_NAME=absensi_sekolah
    ```

5. Jalankan XAMPP dan buka browser, lalu akses `http://localhost/absensi_sekolah`.

## Penggunaan

1. **Login**: Masuk menggunakan akun admin atau guru.
    - **Admin**: Memiliki akses penuh untuk mengelola semua data.
    - **Guru**: Memiliki akses untuk mencatat kehadiran siswa dan melihat laporan.
2. **Manajemen Data**: Tambah, edit, atau hapus data siswa, guru, dan kelas.
3. **Absensi**: Catat kehadiran siswa setiap hari.
4. **Laporan**: Lihat dan cetak laporan kehadiran siswa.

## Kontribusi

Jika Anda ingin berkontribusi pada proyek ini, silakan fork repositori ini dan buat pull request dengan perubahan Anda.

## Lisensi

Proyek ini dilisensikan di bawah [MIT License](https://github.com/abdul-lambada/absensi_sekolah/blob/main/LICENSE).
