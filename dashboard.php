<?php
session_start();

if (!isset($_SESSION['nama'])) {
    header("Location: auth.php");
    exit();
}

require 'koneksi.php';
 
$nama_user = $_SESSION['nama'];
$is_admin  = ($nama_user === 'admin');
 
if ($is_admin && isset($_GET['hapus'])) {
    $id_hapus = (int) $_GET['hapus'];
    $stmt_del = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt_del->bind_param("i", $id_hapus);
    $stmt_del->execute();
    $stmt_del->close();
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>

<body>
    <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h2>
    <a href="logout.php"><button>Logout</button></a>
    <hr>

<?php if ($is_admin): ?>
 
    <p><strong>Menu Admin: Kelola Pengguna</strong></p>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT id, nama FROM users ORDER BY id DESC");
        while ($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $row['id']; ?>"><button>Edit</button></a>
                    <a href="dashboard.php?hapus=<?php echo $row['id']; ?>"
                       onclick="return confirm('Yakin hapus pengguna ini?')"><button>Hapus</button></a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
 
<?php endif; ?>

</body>
</html>