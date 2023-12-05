<?php

require 'vendor/autoload.php'; // Menggunakan Composer untuk mengelola dependensi PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

// Database connection details
include 'connection.php';

// Main script
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the uploaded Excel file
    if (isset($_FILES['excelFile']) && $_FILES['excelFile']['error'] == UPLOAD_ERR_OK) {
        $excelFile = $_FILES['excelFile']['tmp_name'];

        // Load the Excel file with allowOnly setting
        $spreadsheet = IOFactory::load($excelFile, 'Xlsx', ['allowOnly' => 'xlsx']);
        $worksheet = $spreadsheet->getActiveSheet();

        // Iterate through rows and insert data into the 'transaksi' table
        foreach ($worksheet->getRowIterator() as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }

            // Assuming the Excel columns are in the order of 'tid', 'nama_item'
            if (count($rowData) == 2) {
                $tid = $rowData[0];
                $nama_item = $rowData[1];

                // Insert data into the 'transaksi' table
                $stmt = $pdo->prepare('INSERT INTO transaksi (tid, nama_item) VALUES (?, ?)');
                $stmt->execute([$tid, $nama_item]);
            }
        }

        echo 'Import successful!';
    } else {
        echo 'Error uploading the file.';
    }
}

?>
