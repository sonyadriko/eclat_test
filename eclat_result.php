<?php
// connection.php harus di-include di sini
include 'connection.php';
include 'eclat_functions.php'; // Atur file ini sesuai dengan fungsi perhitungan Eclat Anda

// Periksa apakah formulir telah dikirim
if (isset($_POST['calculate'])) {
    // Ambil nilai minimum support dan minimum confidence dari formulir
    $min_support = isset($_POST['min_support']) ? floatval($_POST['min_support']) : 0.0;
    $min_confidence = isset($_POST['min_confidence']) ? floatval($_POST['min_confidence']) : 0.0;

    // Lakukan perhitungan Eclat dengan parameter yang dimasukkan
    $result = calculateEclat($pdo, $min_support, $min_confidence);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perhitungan Eclat</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            overflow-x: auto; /* Tambahkan agar tabel menjadi responsif */
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Hasil Perhitungan Eclat</h2>

    <?php if (isset($result) && !empty($result)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Antecedent</th>
                    <th>Consequent</th>
                    <th>Support Count</th>
                    <th>Confidence (%)</th> <!-- Ganti judul kolom Confidence -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $rule) : ?>
                    <tr>
                        <td><?= isset($rule['antecedent']) && is_array($rule['antecedent']) ? implode(', ', $rule['antecedent']) : ''; ?></td>
                        <td><?= isset($rule['consequent']) && is_array($rule['consequent']) ? implode(', ', $rule['consequent']) : ''; ?></td>
                        <td><?= isset($rule['confidence']) ? number_format($rule['confidence'] * 100, 2) . '%' : ''; ?></td>

                        <td><?= isset($rule['confidence']) ? number_format($rule['confidence'] * 100, 2) . '%' : ''; ?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Tidak ada hasil perhitungan yang ditampilkan.</p>
    <?php endif; ?>
</body>
</html>
