<?php

require '../includes/zklib/zklibrary.php';

$zk = new ZKLibrary('192.168.1.201', 4370);
$zk->connect();
$zk->disableDevice();

$users = $zk->getUser();
$log_kehadiran = $zk->getAttendance();
echo '<pre>';
echo var_dump($log_kehadiran);
echo '</pre>';

// Connection Database
$mysqli = new mysqli("localhost", "root", "", "absensi_sekolah");

if ($mysqli->connect_errno) {
  echo "failed to connect MySQL: " . $mysqli->connect_error;
  exit();
}

// $sql = mysqli_query($mysqli, "SELECT * FROM tbl_kehadiran");

// while ($row = mysqli_fetch_assoc($sql)) {
//   echo $row[];
// }


//  Insert Ke database
foreach ($log_kehadiran as $key => $row) {
  $sql = mysqli_query($mysqli, "INSERT INTO tbl_kehadiran (uid,waktu) VALUES ('$row[3]', '$row[4]')");
}


// Ambil data pengguna dari device
$sql = mysqli_query($mysqli, "SELECT * FROM tbl_kehadiran");
while ($row = mysqli_fetch_assoc($sql)) {
  echo $row['waktu']."<br>";
}
?>

<?php

$zk->enableDevice();
$zk->disconnect();

?>