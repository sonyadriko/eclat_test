<?php
function calculateEclat($pdo, $min_support, $min_confidence) {
    $transactions = fetchDataFromDatabase($pdo);

    // Step 1: Generate frequent 1-itemsets
    $frequentItems = generateFrequentItems($transactions, $min_support);

    // Step 2: Generate frequent itemsets
    $frequentItemsets = generateFrequentItemsets($transactions, $frequentItems, $min_support);

    // Step 3: Generate association rules
    $associationRules = generateAssociationRules($frequentItemsets, $min_confidence);

    // Prepare result with horizontal and vertical itemsets
    $result = [
        'horizontal_itemsets' => array_map('unserialize', array_keys($frequentItemsets)),
        'vertical_itemsets' => $frequentItems,
        'association_rules' => $associationRules,
    ];

    return $result;
}

function fetchDataFromDatabase($pdo) {
    $query = "SELECT tid, nama_item FROM transaksi";
    $statement = $pdo->query($query);
    $transactions = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $transactions;
}

function generateFrequentItems($transactions, $min_support) {
    $itemCounts = [];
    foreach ($transactions as $transaction) {
        foreach ($transaction as $item) {
            $itemCounts[$item] = isset($itemCounts[$item]) ? $itemCounts[$item] + 1 : 1;
        }
    }

    $frequentItems = [];
    foreach ($itemCounts as $item => $count) {
        if ($count >= $min_support) {
            $frequentItems[] = [$item];
        }
    }

    return $frequentItems;
}

function generateFrequentItemsets($transactions, $frequentItems, $min_support) {
    $frequentItemsets = [];
    $transactionCount = count($transactions);

    foreach ($frequentItems as $itemset) {
        // Hitung dukungan untuk setiap itemset
        $supportCount = 0;
        foreach ($transactions as $transaction) {
            if (array_intersect($itemset, $transaction)) {
                $supportCount++;
            }
        }

        // Check if the support meets the minimum support threshold
        if ($supportCount >= $min_support) {
            $frequentItemsets[serialize($itemset)] = $supportCount;
        }
    }

    return $frequentItemsets;
}

function generateAssociationRules($frequentItemsets, $min_confidence) {
    $associationRules = [];

    foreach ($frequentItemsets as $itemset => $supportCount) {
        $itemset = unserialize($itemset); // Kembalikan array dari string serialized

        $itemsetSize = count($itemset);

        if ($itemsetSize > 1) {
            // Generate all possible subsets of the itemset
            $subsets = generateSubsets($itemset);

            foreach ($subsets as $subset) {
                $subsetSupport = isset($frequentItemsets[serialize($subset)]) ? $frequentItemsets[serialize($subset)] : 0;

                if ($subsetSupport > 0) {
                    $confidence = $supportCount / $subsetSupport;

                    // Check if the confidence meets the minimum confidence threshold
                    if ($confidence >= $min_confidence) {
                        $associationRules[] = [
                            'antecedent' => $subset,
                            'consequent' => array_values(array_diff($itemset, $subset)),
                            'support_count' => $supportCount,
                            'confidence' => $confidence,
                        ];
                    }
                }
            }
        }
    }

    return $associationRules;
}

function generateSubsets($set) {
    $subsets = [[]];
    $setSize = count($set);

    for ($i = 0; $i < $setSize; $i++) {
        $currentSize = count($subsets);
        for ($j = 0; $j < $currentSize; $j++) {
            $subsets[] = array_merge($subsets[$j], [$set[$i]]);
        }
    }

    // Remove the empty set
    array_shift($subsets);

    return $subsets;
}
?>
