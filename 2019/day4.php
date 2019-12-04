<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$START = 178416;
$END = 676461;

function isAscendingOrder(int $number): bool {
    $str = str_split((string)$number);

    for ($i = 0; $i < count($str) - 1; $i++) {
        if ((int)$str[$i + 1] < (int)$str[$i]) {
            return false;
        }
    }

    return true;
}

function hasRepeatingDigits(int $number): bool {
    return preg_match('/(\d)\1/', (string)$number);
}

function part1(): int {
    global $START, $END;

    $total = 0;

    for ($i = $START; $i < $END; $i++) {
        if (isAscendingOrder($i) && hasRepeatingDigits($i)) {
            $total++;
        }
    }

    return $total;
}

printf("Part 1: %d\n", part1());

function hasAtLeastOneRepeatingPair(int $number): bool {
    $results = [];
    preg_match_all('/((\d)\2\2*)/m', (string)$number, $results, PREG_SET_ORDER);

    foreach ($results as $result) {
        if (strlen($result[0]) === 2) {
            return true;
        }
    }

    return false;
}

function part2(): int {
    global $START, $END;

    $total = 0;

    for ($i = $START; $i < $END; $i++) {
        if (isAscendingOrder($i) && hasRepeatingDigits($i) && hasAtLeastOneRepeatingPair($i)) {
            $total++;
        }
    }

    return $total;
}

printf("Part 2: %d\n", part2());
