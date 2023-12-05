<?php
// connection.php harus di-include di sini
include 'connection.php';
include 'eclat_functions.php'; // Atur file ini sesuai dengan fungsi perhitungan Eclat Anda

// Lakukan perhitungan Eclat
$result = calculateEclat($pdo);

// Tampilkan hasil perhitungan
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perhitungan Eclat</title>
</head>
<body>
    <h2>Hasil Perhitungan Eclat</h2>
    <pre>
    <?php print_r($result); ?>
    </pre>
</body>
</html>
