<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "db_mahasiswa");

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari database
$query = "SELECT jurusan, COUNT(*) as jumlah FROM mahasiswa GROUP BY jurusan";
$result = $conn->query($query);

// Simpan hasil query dalam array
$jurusan = [];
$jumlah = [];

while ($row = $result->fetch_assoc()) {
    $jurusan[] = $row['jurusan'];
    $jumlah[] = $row['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafik Mahasiswa & Export PDF</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        canvas {
            max-width: 80%;
            margin: 20px auto;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <h2>Grafik Mahasiswa Berdasarkan Jurusan</h2>
    <canvas id="myChart" width="400" height="200"></canvas>
    <br>
    <button id="downloadPdf">Download PDF</button>

    <script>
        // Data dari PHP ke JavaScript
        var jurusan = <?php echo json_encode($jurusan); ?>;
        var jumlah = <?php echo json_encode($jumlah); ?>;

        // Inisialisasi grafik
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: jurusan,
                datasets: [{
                    label: 'Jumlah Mahasiswa',
                    data: jumlah,
                    backgroundColor: ['red', 'blue', 'green', 'orange', 'purple'],
                    borderColor: 'black',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    tooltip: { enabled: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Fungsi untuk mengunduh grafik sebagai PDF
        document.getElementById('downloadPdf').addEventListener('click', function() {
            setTimeout(function() {
                const { jsPDF } = window.jspdf;
                var pdf = new jsPDF();
                var canvas = document.getElementById('myChart');
                var imgData = canvas.toDataURL('image/png');

                pdf.text("Grafik Mahasiswa Berdasarkan Jurusan", 10, 10);
                pdf.addImage(imgData, 'PNG', 10, 20, 180, 100);
                pdf.save("grafik_mahasiswa.pdf");
            }, 500);
        });
    </script>
</body>
</html>
