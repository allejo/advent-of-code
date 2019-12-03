<?php

function getOpCodes(int $arg1, int $arg2): array {
    $contents = file_get_contents(__DIR__ . '/day2.input.txt');

    $opCodes = explode(',', $contents);
    $opCodes[1] = $arg1;
    $opCodes[2] = $arg2;

    return $opCodes;
}

function executeInstruction(int $start, array &$instructions): bool {
    $operation = $instructions[$start];

    if ($operation == 99) {
        return false;
    }

    $value1 = $instructions[$instructions[$start + 1]];
    $value2 = $instructions[$instructions[$start + 2]];
    $target = $instructions[$start + 3];

    $result = 0;

    switch ($operation) {
        case "1":
            $result = $value1 + $value2;
            break;

        case "2":
            $result = $value1 * $value2;
            break;
        
        default:
            throw InvalidArgumentException('Invalid opcode');
    }

    $instructions[$target] = $result;

    return true;
}

function executeProgram(array &$instructions): array {
    $pos = 0;

    while (executeInstruction($pos, $instructions)) {
        $pos += 4;
    }

    return $instructions;
}

function part1_unitTests(): void {
    $dataStructure = [
        ['1,0,0,0,99', '2,0,0,0,99'],
        ['2,3,0,3,99', '2,3,0,6,99'],
        ['2,4,4,5,99,0', '2,4,4,5,99,9801'],
        ['1,1,1,4,99,5,6,0,99', '30,1,1,4,2,5,6,0,99'],
    ];

    foreach ($dataStructure as $data) {
        $arr = explode(',', $data[0]);
        executeProgram($arr);

        if (implode(',', $arr) === $data[1]) {
            print('.');
        } else {
            print('F');
        }
    }

    print("\n");
}

function part1(): void {
    $opCodes = getOpCodes(12, 2);
    executeProgram($opCodes);

    print("Part 1\n");
    print("------\n");
    printf("solution: %d\n", $opCodes[0]);
    printf("instructions: %s\n", implode(',', $opCodes));
}

part1_unitTests();
part1();

function part2(): void {
    $target = 19690720;
    $min = 0;
    $max = 99;

    $noun = -1;
    $verb = -1;

    for ($i = $min; $i <= $max; $i++) {
        for ($j = $min; $j <= $max; $j++) {
            $opCodes = getOpCodes($i, $j);
            executeProgram($opCodes);

            if ($opCodes[0] === $target) {
                $noun = $i;
                $verb = $j;
                break 2;
            }
        }
    }

    $solution = 100 * $noun + $verb;

    print("Part 2\n");
    print("------\n");
    print("noun: $i; verb: $j\n");
    print("solution: $solution\n");
}

part2();
