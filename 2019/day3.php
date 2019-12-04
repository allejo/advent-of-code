<?php

function getInputs(): array {
    $content = file_get_contents(__DIR__ . '/day3.input.txt');
    $inputs = explode("\n", trim($content));

    return [
        explode(',', $inputs[0]),
        explode(',', $inputs[1]),
    ];
}

function getCoordinates(array $instructions): array {
    $coordinates = [];

    $lastX = 0;
    $lastY = 0;
    $step = 0;

    foreach ($instructions as $instruction) {
        $direction = substr($instruction, 0, 1);
        $distance = substr($instruction, 1);

        for ($i = 0; $i < $distance; $i++) {
            if ($direction === 'R') {
                $lastX++;
            } elseif ($direction === 'L') {
                $lastX--;
            } elseif ($direction === 'U') {
                $lastY++;
            } elseif ($direction === 'D') {
                $lastY--;
            }

            $coordinates[sprintf('%d,%d', $lastX, $lastY)] = ++$step;
        }
    }

    return $coordinates;
}

function findSmallestManhattanDistance(array $path1, array $path2): int {
    $lowest = 99999;

    foreach ($path1 as $coordinate => $step) {
        if (!isset($path2[$coordinate])) {
            continue;
        }

        list($x, $y) = explode(',', $coordinate);
        $mDist = abs($x) + abs($y);

        if ($mDist < $lowest) {
            $lowest = $mDist;
        }
    }

    return $lowest;
}

function part1_unitTests(): void {
    $dataStructure = [
        [6, ['R8,U5,L5,D3', 'U7,R6,D4,L4']],
        [159, ['R75,D30,R83,U83,L12,D49,R71,U7,L72', 'U62,R66,U55,R34,D71,R55,D58,R83']],
        [135, ['R98,U47,R26,D63,R33,U87,L62,D20,R33,U53,R51', 'U98,R91,D20,R16,D67,R40,U7,R15,U6,R7']],
    ];

    print('Tests: ');

    foreach ($dataStructure as $test) {
        $mDistance = part1([
            explode(',', $test[1][0]),
            explode(',', $test[1][1]),
        ]);

        if ($test[0] === $mDistance) {
            print('.');
        } else {
            print('F');
        }
    }

    print("\n");
}

function part1(array $movements): int {
    $path1 = getCoordinates($movements[0]);
    $path2 = getCoordinates($movements[1]);

    return findSmallestManhattanDistance($path1, $path2);
}

part1_unitTests();
printf("Part 1: %s\n", part1(getInputs()));

function findSmallestStepCount(array $path1, array $path2) {
    $lowest = 99999;

    foreach ($path1 as $coordinate => $step) {
        if (!isset($path2[$coordinate])) {
            continue;
        }

        $step1 = $path1[$coordinate];
        $step2 = $path2[$coordinate];
        $stepCount = $step1 + $step2;

        if ($stepCount < $lowest) {
            $lowest = $stepCount;
        }
    }

    return $lowest;
}

function part2(array $movements) {
    $path1 = getCoordinates($movements[0]);
    $path2 = getCoordinates($movements[1]);

    return findSmallestStepCount($path1, $path2);
}

function part2_unitTests(): void {
    $dataStructure = [
        [30, ['R8,U5,L5,D3', 'U7,R6,D4,L4']],
        [610, ['R75,D30,R83,U83,L12,D49,R71,U7,L72', 'U62,R66,U55,R34,D71,R55,D58,R83']],
        [410, ['R98,U47,R26,D63,R33,U87,L62,D20,R33,U53,R51', 'U98,R91,D20,R16,D67,R40,U7,R15,U6,R7']],
    ];

    print('Tests: ');

    foreach ($dataStructure as $test) {
        $mDistance = part2([
            explode(',', $test[1][0]),
            explode(',', $test[1][1]),
        ]);

        if ($test[0] === $mDistance) {
            print('.');
        } else {
            print('F');
        }
    }

    print("\n");
}

part2_unitTests();
printf("Part 2: %s\n", part2(getInputs()));
