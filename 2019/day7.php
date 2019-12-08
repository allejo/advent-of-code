<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ProgramEnd extends Exception {}
class ProgramPaused extends Exception {}
class InvalidOpCode extends Exception {}

/**
 * @param $array
 *
 * @see https://stackoverflow.com/a/24517966
 *
 * @return array
 */
function computePermutations($array) {
    $result = [];

    $recurse = function($array, $start_i = 0) use (&$result, &$recurse) {
        if ($start_i === count($array)-1) {
            array_push($result, $array);
        }

        for ($i = $start_i; $i < count($array); $i++) {
            //Swap array value at $i and $start_i
            $t = $array[$i]; $array[$i] = $array[$start_i]; $array[$start_i] = $t;

            //Recurse
            $recurse($array, $start_i + 1);

            //Restore old order
            $t = $array[$i]; $array[$i] = $array[$start_i]; $array[$start_i] = $t;
        }
    };

    $recurse($array);

    return $result;
}

$GLOBAL_INPUT = null;

function askUserInput(): ?int {
    global $GLOBAL_INPUT;

    if ($GLOBAL_INPUT !== null) {
        return array_shift($GLOBAL_INPUT);
    }

    echo "Input: ";
    return (int)trim(fgets(STDIN));
}

function getValue(array &$instructions, int $position, int $mode): int {
    if ($mode == 1) {
        return $instructions[$position];
    }

    return $instructions[$instructions[$position]];
}

function executeInstruction(int $start, array &$instructions, ?int &$pointer, ?int &$output): int {
    $operation = (string)$instructions[$start];

    if ($operation == 99) {
        throw new ProgramEnd();
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

        if ($input === null) {
            throw new ProgramPaused('', $start);
        }

        $value = getValue($instructions, $start + 1, $modeBits[0] ?? 1);
        $instructions[$value] = $input;

        return 2;
    } elseif ($operation == 4) {
        $value = getValue($instructions, $start + 1, $modeBits[0] ?? 0);

        // printf("%d\n", $value);
        $output = $value;

        return 2;
    } elseif ($operation == 5 || $operation == 6) {
        $arg1 = getValue($instructions, $start + 1, $modeBits[0] ?? 0);
        $arg2 = getValue($instructions, $start + 2, $modeBits[1] ?? 0);

        if (($operation == 5 && $arg1 != 0) || ($operation == 6 && $arg1 == 0)) {
            $pointer = $arg2;
            return 0;
        }

        return 3;
    } elseif ($operation == 7 || $operation == 8) {
        $arg1 = getValue($instructions, $start + 1, $modeBits[0] ?? 0);
        $arg2 = getValue($instructions, $start + 2, $modeBits[1] ?? 0);
        $pos = getValue($instructions, $start + 3, $modeBits[2] ?? 1);

        if ($operation == 7) {
            $instructions[$pos] = (int)($arg1 < $arg2);
        } else {
            $instructions[$pos] = (int)($arg1 == $arg2);
        }

        return 4;
    }

    throw new InvalidOpCode("Invalid opcode $operation");
}

function executeProgram(array &$instructions, bool $throwOnEnd = false, int $start = 0, ?int &$pointer = null): int {
    $result = -9999;
    $offset = $start;

    while(true) {
        try {
            $offset += executeInstruction($offset, $instructions, $offset, $result);
        } catch (ProgramEnd | ProgramPaused $e) {
            if ($e instanceof ProgramPaused) {
                $pointer = $e->getCode();
            }

            if ($throwOnEnd && $e instanceof ProgramEnd) {
                throw new ProgramEnd('', $result);
            }

            break;
        }
    }

    return $result;
}

// Part 1
//$raw = '3,31,3,32,1002,32,10,32,1001,31,-2,31,1007,31,0,33,1002,33,7,33,1,33,31,31,1,32,31,31,4,31,99,0,0,0';

// Part 2
//$raw = '3,26,1001,26,-4,26,3,27,1002,27,2,27,1,27,26,27,4,27,1001,28,-1,28,1005,28,6,99,0,0,5';

$raw = file_get_contents(__DIR__ . '/day7.input.txt');

function part1() {
    global $GLOBAL_INPUT, $raw;

    $amps = [0, 1, 2, 3, 4];
    $permutations = computePermutations($amps);

    $maxTotal = -9999;
    $maxID = '';

    foreach ($permutations as $phaseSettings) {
        $prevOutput = 0;

        foreach ($phaseSettings as $phaseSetting) {
            $GLOBAL_INPUT = [$phaseSetting, $prevOutput];

            $instructions = explode(',', $raw);
            $prevOutput = executeProgram($instructions);
        }

        if ($prevOutput > $maxTotal) {
            $maxTotal = $prevOutput;
            $maxID = implode('', $phaseSettings);
        }
    }

    printf("Part 1 Answer: ( %d ) Seq: %s\n", $maxTotal, $maxID);
}

//part1();

function part2() {
    global $GLOBAL_INPUT, $raw;

    $amps = [5, 6, 7, 8, 9];
    $permutations = computePermutations($amps);
    $units = [];
    $pointers = [];

    $maxTotal = -9999;
    $maxID = '';

    foreach ($permutations as $phaseSettings) {
        $prevOutput = 0;

        // Persist our amplifier's state between runs
        foreach ($phaseSettings as $i => $phaseSetting) {
            $GLOBAL_INPUT = [$phaseSetting, $prevOutput];

            $units[$i] = explode(',', $raw);
            $prevOutput = executeProgram($units[$i], false, 0, $pointers[$i]);
        }

        while (true) {
            $done = false;

            foreach ($units as $i => &$unit) {
                $GLOBAL_INPUT = [$prevOutput];

                try {
                    $prevOutput = executeProgram($unit, true, $pointers[$i], $pointers[$i]);
                } catch (ProgramEnd $e) {
                    $prevOutput = $e->getCode();
                    $done = true;
                }
            }

            if ($done) {
                break;
            }
        }

        if ($prevOutput > $maxTotal) {
            $maxTotal = $prevOutput;
            $maxID = implode('', $phaseSettings);
        }
    }

    printf("Part 2 Answer: ( %d ) Seq: %s\n", $maxTotal, $maxID);
}

part2();
