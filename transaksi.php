<?php
// connection.php harus di-include di sini
include 'connection.php';

// Ambil data transaksi dari database
$stmt = $pdo->prepare('SELECT * FROM transaksi');
$stmt->execute();
$transaksiData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Transaksi</title>
</head>
<body>
    <h2>Data Transaksi</h2>
    <table border="1">
        <thead>
            <tr>
                <th>TID</th>
                <th>Nama Item</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transaksiData as $data): ?>
                <tr>
                    <td><?= $data['tid']; ?></td>
                    <td><?= $data['nama_item']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
