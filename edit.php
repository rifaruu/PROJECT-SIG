<?php
include 'db_connection.php';

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$id = $_GET['id'] ?? '';
if (!$id) {
    die("ID tidak ditemukan.");
}

$query = "SELECT * FROM health_services WHERE id=$id";
$result = $conn->query($query);

if (!$result || $result->num_rows == 0) {
    die("Data tidak ditemukan.");
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="card p-4 shadow">
        <h2 class="text-center text-warning">Edit Data</h2>
        <form action="crud.php" method="post">
            <input type="hidden" name="id" value="<?= $data['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="name" value="<?= $data['name']; ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <input type="text" name="address" value="<?= $data['address']; ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Latitude</label>
                <input type="number" step="any" name="latitude" value="<?= $data['latitude']; ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Longitude</label>
                <input type="number" step="any" name="longitude" value="<?= $data['longitude']; ?>" class="form-control" required>
            </div>
            <button type="submit" name="update" class="btn btn-primary w-100">Simpan</button>
            <a href="crud.php" class="btn btn-secondary w-100 mt-2">Kembali</a>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
