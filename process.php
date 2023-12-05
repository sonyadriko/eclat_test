<?php

function calculateEclatFromDatabase(PDO $pdo, $minSupport = 2) {
    // Fetch data from the database table 'transaksi'
    $stmt = $pdo->prepare('SELECT tid, nama_item FROM transaksi');
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process the transactions to calculate Eclat
    $itemsets = [];

    foreach ($transactions as $transaction) {
        $items = str_split(trim($transaction['nama_item']));
        foreach ($items as $item) {
            $itemsets[$item][] = $transaction['tid'];
        }
    }

    $result = [];

    while (!empty($itemsets)) {
        $frequentItemsets = [];
        foreach ($itemsets as $item => $transactionIds) {
            $support = count($transactionIds);
            if ($support >= $minSupport) {
                $frequentItemsets[$item] = $support;
            }
        }

        $result[] = $frequentItemsets;

        // Generate (k+1)-itemsets
        $itemsets = generateNextItemsets($frequentItemsets, $transactions);
    }

    return $result;
}

function generateNextItemsets($itemsets, $transactions) {
    $nextItemsets = [];

    $keys = array_keys($itemsets);
    $count = count($keys);

    for ($i = 0; $i < $count; $i++) {
        for ($j = $i + 1; $j < $count; $j++) {
            $itemsetA = $keys[$i];
            $itemsetB = $keys[$j];

            // Join itemsets
            $newItemset = $itemsetA . $itemsetB;
            
            // Count support for the new itemset
            $support = countTransactionsContainingItemset($newItemset, $transactions);

            // Add to next itemsets if support is sufficient
            if ($support > 0) {
                $nextItemsets[$newItemset] = $support;
            }
        }
    }

    return $nextItemsets;
}

function countTransactionsContainingItemset($itemset, $transactions) {
    $count = 0;

    foreach ($transactions as $transaction) {
        if (strpos($transaction['nama_item'], $itemset) !== false) {
            $count++;
        }
    }

    return $count;
}

include 'connection.php';

// Main script
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Call the function to calculate Eclat from the database
    $result = calculateEclatFromDatabase($pdo);

    echo '<pre>';
    print_r($result);
    echo '</pre>';
}

?>
