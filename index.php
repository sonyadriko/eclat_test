<?php

require 'vendor/autoload.php'; // Menggunakan Composer untuk mengelola dependensi PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

// Database connection details
include 'connection.php';

// Main script
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['import'])) {
        // Process the uploaded Excel file for import
        if (isset($_FILES['excelFile']) && $_FILES['excelFile']['error'] == UPLOAD_ERR_OK) {
            $excelFile = $_FILES['excelFile']['tmp_name'];

            // Load the Excel file with allowOnly setting
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($excelFile);

            $worksheet = $spreadsheet->getActiveSheet();

            // Iterate through rows and insert data into the 'transaksi' table
            foreach ($worksheet->getRowIterator() as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }

                // Assuming the Excel columns are in the order of 'tid', 'nama_item'
                // ...
                if (count($rowData) == 2) {
                    $tid = $rowData[0];
                    $nama_item = $rowData[1];

                    // Pastikan $tid tidak kosong
                    if (!empty($tid)) {
                        // Insert data into the 'transaksi' table
                        $stmt = $pdo->prepare('INSERT INTO transaksi (tid, nama_item) VALUES (?, ?)');
                        
                        if ($stmt->execute([$tid, $nama_item])) {
                            echo 'Sukses: Data berhasil diimport.';
                        } else {
                            echo 'Error: Gagal menyimpan data.';
                        }
                    } else {
                        echo 'Error: tid is empty.';
                    }
                }
                // ...

            }

            echo 'Import successful!';
        } else {
            echo 'Error uploading the file.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eclat Calculator</title>
</head>
<body>
    <h2>Eclat Calculator</h2>
    <!-- Import form -->
    <form action="index.php" method="post" enctype="multipart/form-data">
        <label for="excelFile">Choose Excel File:</label>
        <input type="file" name="excelFile" id="excelFile" accept=".xls, .xlsx" required>
        <button type="submit" name="import">Import</button>
    </form>

    <!-- Calculate form -->
    <!-- Calculate form -->
    <form action="eclat_result.php" method="post">
        <label for="min_support">Minimum Support:</label>
        <input type="text" name="min_support" id="min_support" required>

        <label for="min_confidence">Minimum Confidence:</label>
        <input type="text" name="min_confidence" id="min_confidence" required>

        <button type="submit" name="calculate">Calculate Eclat</button>
    </form>
</body>
</html>
