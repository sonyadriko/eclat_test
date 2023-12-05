<?php

function calculateEclat(PDO $pdo, $minSupport = 2) {
    // Fetch data from the 'transaksi' table
    $stmt = $pdo->query('SELECT tid, nama_item FROM transaksi');
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process transactions to build itemsets
    $itemsets = [];

    foreach ($transactions as $transaction) {
        $items = str_split(trim($transaction['nama_item']));
        foreach ($items as $item) {
            $itemsets[$item][] = $transaction['tid'];
        }
    }

    $result = [];

    while (!empty($itemsets)) {
        $frequentItemsets = array_filter($itemsets, function ($transactionIds) use ($minSupport) {
            return count($transactionIds) >= $minSupport;
        });

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
                $nextItemsets[$newItemset] = getTransactionIdsContainingItemset($newItemset, $transactions);
            }
        }
    }

    return $nextItemsets;
}

function countTransactionsContainingItemset($itemset, $transactions) {
    return count(array_filter($transactions, function ($transaction) use ($itemset) {
        return strpos($transaction['nama_item'], $itemset) !== false;
    }));
}

function getTransactionIdsContainingItemset($itemset, $transactions) {
    return array_map(function ($transaction) use ($itemset) {
        return $transaction['tid'];
    }, array_filter($transactions, function ($transaction) use ($itemset) {
        return strpos($transaction['nama_item'], $itemset) !== false;
    }));
}
