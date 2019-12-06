<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getOpCodes(): array {
    $contents = file_get_contents(__DIR__ . '/day5.input.txt');

    return explode(',', $contents);
}

function askUserInput(): int {
    echo "Input: ";
    return (int)trim(fgets(STDIN));
}

function getValue(array &$instructions, int $position, int $mode): int {
    if ($mode == 1) {
        return $instructions[$position];
    }

    return $instructions[$instructions[$position]];
}

function executeInstruction(int $start, array &$instructions): int {
    $operation = (string)$instructions[$start];

    if ($operation == 99) {
        exit(0);
    }

    $insLen = strlen($operation);
    $needsParsing = $insLen > 1;
    $modeBits = [];

    if ($needsParsing) {
        $modeBits = str_split(substr($operation, 0, $insLen - 2));
        $operation = $operation[$insLen - 2] . $operation[$insLen - 1];
        $modeBits = array_reverse($modeBits);
    }

    if ($operation == 1 || $operation == 2) {
        $arg1 = getValue($instructions, $start + 1, ($modeBits[0] ?? 0));
        $arg2 = getValue($instructions, $start + 2, ($modeBits[1] ?? 0));
        $pos = getValue($instructions, $start + 3, ($modeBits[2] ?? 1));

        if ($operation == 1) {
            $instructions[$pos] = $arg1 + $arg2;
        } else {
            $instructions[$pos] = $arg1 * $arg2;
        }

        return 4;
    } elseif ($operation == 3) {
        $input = askUserInput();
        $value = getValue($instructions, $start + 1, $modeBits[0] ?? 1);
        $instructions[$value] = $input;

        return 2;
    } elseif ($operation == 4) {
        $value = getValue($instructions, $start + 1, $modeBits[0] ?? 0);
        printf("%d\n", $value);

        return 2;
    }

    throw new \Error('Invalid opcode');
}

function executeProgram(array &$instructions) {
    $offset = 0;

    while(true) {
        $offset += executeInstruction($offset, $instructions);
    }
}

function part1() {
    $opCodes = getOpCodes();
    executeProgram($opCodes);
}

part1();
