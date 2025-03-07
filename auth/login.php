<?php
session_start();
include '../includes/db.php';

$error = ""; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nip = $_POST['nip'];
    $password = $_POST['password'];

    // Query untuk mencari user berdasarkan NIP
    $stmt = $conn->prepare("SELECT * FROM Guru WHERE nip = :nip");
    $stmt->bindParam(':nip', $nip);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Simpan data user ke session
        $_SESSION['user'] = [
            'id' => $user['id_guru'],       // Pastikan ini sesuai dengan kolom di database
            'nama' => $user['nama_guru'],
            'role' => $user['role']
        ];

        // Redirect berdasarkan role
        if ($user['role'] === 'admin') {
            header("Location: ../admin/index.php");
        } else {
            header("Location: ../guru/index.php");
        }
        exit;
    } else {
        $error = "NIP atau password salah."; // Set error message
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Management Salassika</title>
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../assets/css/sb-admin-2.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center py-2 pb-4">
                                <h1 class="h4 text-primary"><strong>SISTEM INFORMASI MANAGEMENT</strong></h1>
                                <h5 class="text-primary"><b>SALASSIKA</b></h5>
                            </div>
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>
                            <form method="POST" action="">
                                <div class="form-group">
                                    <input type="text" name="nip" class="form-control" placeholder="NIP" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>