<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel to Database</title>
</head>
<body>
    <h2>Import Excel to Database</h2>
    <form action="import.php" method="post" enctype="multipart/form-data">
        <label for="excelFile">Choose Excel File:</label>
        <input type="file" name="excelFile" id="excelFile" accept=".xls, .xlsx" required>
        <button type="submit">Import</button>
    </form>
</body>
</html>
