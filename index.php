<?php

include 'db_connection.php';

$sql = "SELECT id, name, latitude, longitude FROM health_services";
$result = $conn->query($sql);

$health_services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $health_services[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Kecamatan & Layanan Kesehatan Banyumas</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
        }

        button {
            background-color: #3498db;
            border: none;
            color: white;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        #controls {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
            align-items: center;
        }

        select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        #distance-result {
            text-align: center;
            font-size: 18px;
            color: #2c3e50;
            margin-top: 10px;
        }

        #map {
            height: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .data-btn {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .data-btn a {
            text-decoration: none;
        }
    </style>
</head>



<body>

    <h1>Peta Kecamatan & Layanan Kesehatan Kabupaten Banyumas</h1>

    <div style="position: absolute; top: 20px; right: 40px; z-index: 1000;">
        <img src="img/logo.png" alt="Logo" style="height: 80px;">
    </div>

    <div class="data-btn">
        <a href="crud.php" target="_blank">
            <button>Kelola Data</button>
        </a>
    </div>

    <div id="controls">
        <label for="loc1">Pilih Fasilitas 1:</label>
        <select id="loc1">
            <?php foreach ($health_services as $service): ?>
            <option value="<?= $service['latitude'] . ',' . $service['longitude'] ?>">
                <?= htmlspecialchars($service['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <label for="loc2">Pilih Fasilitas 2:</label>
        <select id="loc2">
            <?php foreach ($health_services as $service): ?>
            <option value="<?= $service['latitude'] . ',' . $service['longitude'] ?>">
                <?= htmlspecialchars($service['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <button onclick="calculateDistance()">Hitung Jarak</button>
        <button onclick="resetDistance()">Reset</button>
    </div>

    <p id="distance-result"></p>
    <div id="map"></div>

    <script type="text/javascript" src="data/kecamatan.json"></script>

    <script>
        const map = L.map('map').setView([-7.4501619925610265, 109.16218062235065], 11);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        L.geoJSON(kecamatan, {
            style: function(feature) {
                return {
                    color: 'blue',
                    weight: 2
                };
            }
        }).addTo(map);

        const healthServices = <?php echo json_encode($health_services); ?>;
        healthServices.forEach(service => {
            L.marker([service.latitude, service.longitude]).addTo(map)
                .bindPopup('<b>' + service.name + '</b>');
        });

        function toRad(deg) {
            return deg * Math.PI / 180;
        }

        function haversine(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        let currentLine = null;

        function calculateDistance() {
            const loc1 = document.getElementById('loc1').value.split(',');
            const loc2 = document.getElementById('loc2').value.split(',');

            const lat1 = parseFloat(loc1[0]);
            const lon1 = parseFloat(loc1[1]);
            const lat2 = parseFloat(loc2[0]);
            const lon2 = parseFloat(loc2[1]);

            const distance = haversine(lat1, lon1, lat2, lon2);
            document.getElementById('distance-result').innerHTML =
                `<b>Jarak:</b> ${distance.toFixed(2)} km`;

            if (currentLine) {
                map.removeLayer(currentLine);
            }

            currentLine = L.polyline([
                [lat1, lon1],
                [lat2, lon2]
            ], {
                color: 'red',
                weight: 3,
                dashArray: '5, 10'
            }).addTo(map);

            map.fitBounds(currentLine.getBounds());
        }

        function resetDistance() {
            if (currentLine) {
                map.removeLayer(currentLine);
                currentLine = null;
            }
            document.getElementById('distance-result').innerHTML = '';
            document.getElementById('loc1').selectedIndex = 0;
            document.getElementById('loc2').selectedIndex = 0;
            map.setView([-7.4501619925610265, 109.16218062235065], 11);
        }
    </script>

</body>

<footer style="text-align: center; margin-top: 30px; padding: 15px 10px; font-size: 14px; color: #777;">
    &copy; <?= date("Y") ?> Sistem Informasi Peta Layanan Kesehatan Kabupaten Banyumas. Dibuat oleh Rifal Abdussyakur IF6B.
</footer>


</html>
