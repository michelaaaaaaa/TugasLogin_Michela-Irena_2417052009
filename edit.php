<?php
session_start();

if (!isset($_SESSION['nama']) || $_SESSION['nama'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

require 'koneksi.php';

$id = (int) $_GET['id'];
$pesan = "";

$stmt_get = $conn->prepare("SELECT nama FROM users WHERE id = ?");
$stmt_get->bind_param("i", $id);
$stmt_get->execute();
$stmt_get->bind_result($nama_lama);
$stmt_get->fetch();
$stmt_get->close();

if (empty($nama_lama)) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $nama_baru     = trim($_POST['nama']);
    $password_baru = $_POST['password'];

    if (empty($nama_baru)) {
        $pesan = "Nama pengguna tidak boleh kosong.";
    } else {
        $hashed_password_baru = password_hash($password_baru, PASSWORD_BCRYPT);

        $stmt_up = $conn->prepare("UPDATE users SET nama = ?, password = ? WHERE id = ?");
        $stmt_up->bind_param("ssi", $nama_baru, $hashed_password_baru, $id);

        if ($stmt_up->execute()) {
            $stmt_up->close();
            header("Location: dashboard.php");
            exit();
        } else {
            $pesan = "Gagal memperbarui data: " . $stmt_up->error;
            $stmt_up->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit</title>
</head>
<body>

<?php if ($pesan != "") echo "<p><strong>$pesan</strong></p>"; ?>

<h2>Edit Data Pengguna</h2>

<form method="POST" action="">
    <label>Nama Pengguna:</label><br>
    <input type="text" name="nama"
           value="<?php echo htmlspecialchars($nama_lama); ?>"
           placeholder="Nama Pengguna" required><br><br>

    <label>Password Baru:</label><br>
    <input type="password" name="password"
           placeholder="Masukkan password baru" required><br><br>

    <button type="submit" name="update">Simpan Perubahan</button>
</form>

<br>
<a href="dashboard.php"><button>Batal</button></a>

</body>
</html>