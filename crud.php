<?php
include 'db_connection.php';

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $address = $_POST['address'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $sql = "INSERT INTO health_services (name, address, latitude, longitude) 
                VALUES ('$name', '$address', $latitude, $longitude)";
        $conn->query($sql);
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $address = $_POST['address'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $sql = "UPDATE health_services SET name='$name', address='$address', latitude=$latitude, longitude=$longitude WHERE id=$id";
        $conn->query($sql);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM health_services WHERE id=$id";
        $conn->query($sql);

        // Reset AUTO_INCREMENT jika tabel kosong
        $check = $conn->query("SELECT COUNT(*) as total FROM health_services")->fetch_assoc();
        if ($check['total'] == 0) {
            $conn->query("ALTER TABLE health_services AUTO_INCREMENT = 1");
        }
    }
}

$result = $conn->query("SELECT * FROM health_services");

if (!$result) {
    die("Error dalam query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Layanan Kesehatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h1 class="text-center text-primary">Data Layanan Kesehatan</h1>
    <div class="text-center mb-3">
        <a href="index.php" class="btn btn-secondary">Halaman Utama</a>
    </div>
    <div class="card p-3 shadow">
        <table class="table table-striped table-hover">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['name']; ?></td>
                    <td><?= $row['address']; ?></td>
                    <td><?= $row['latitude']; ?></td>
                    <td><?= $row['longitude']; ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form action="crud.php" method="post" class="d-inline">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <div class="card p-3 shadow mt-4">
        <h2 class="text-center text-success">Tambah Data</h2>
        <form action="crud.php" method="post">
            <div class="mb-3">
                <input type="text" name="name" class="form-control" placeholder="Nama" required>
            </div>
            <div class="mb-3">
                <input type="text" name="address" class="form-control" placeholder="Alamat" required>
            </div>
            <div class="mb-3">
                <input type="number" step="any" name="latitude" class="form-control" placeholder="Latitude" required>
            </div>
            <div class="mb-3">
                <input type="number" step="any" name="longitude" class="form-control" placeholder="Longitude" required>
            </div>
            <button type="submit" name="add" class="btn btn-primary w-100">Tambah</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
