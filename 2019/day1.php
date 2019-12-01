<?php

function getInputs(): array
{
    $content = file_get_contents(__DIR__ . '/day1.input.txt');
    return explode("\n", $content);
}

function calculateMass(int $num): int
{
    return floor($num / 3) - 2;
}

function part1()
{
    $sum = 0;

    foreach (getInputs() as $input) {
        $sum += calculateMass($input);
    }
    
    return $sum;
}

echo part1() . "\n";

function calculateRecursiveMass(int $num): int
{
    $sum = 0;
    $value = $num;

    while (true) {
        $value = calculateMass($value);

        if ($value <= 0) {
            break;
        }

        $sum += $value;
    }

    return $sum;
}

function part2()
{
    $sum = 0;

    foreach (getInputs() as $input) {
        $sum += calculateRecursiveMass($input);
    }

    return $sum;
}

echo part2() . "\n";
