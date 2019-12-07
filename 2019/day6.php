<?php

class Orbit {
    /** @var string */
    public $name;

    /** @var Orbit[] */
    public $childOrbits = [];

    /** @var Orbit|null */
    public $parentOrbit = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addOrbit(Orbit &$orbit)
    {
        $this->childOrbits[$orbit->name] = &$orbit;
    }

    public function fromOrbit(Orbit &$orbit)
    {
        $this->parentOrbit = &$orbit;
    }
}

function getSampleOrbitsPart1() {
    return <<<ORB
COM)B
B)C
C)D
D)E
E)F
B)G
G)H
D)I
E)J
J)K
K)L
ORB;
}

function getSampleOrbitsPart2() {
    return <<<ORB
COM)B
B)C
C)D
D)E
E)F
B)G
G)H
D)I
E)J
J)K
K)L
K)YOU
I)SAN
ORB;
}

function getRealOrbits() {
    return file_get_contents(__DIR__ . '/day6.input.txt');
}

function getOrbits() {
//    $raw = getSampleOrbitsPart1();
//    $raw = getSampleOrbitsPart2();
    $raw = getRealOrbits();
    $matches = [];

    preg_match_all('/(\w*)\)(\w*)\n?/m', $raw, $matches, PREG_SET_ORDER, 0);

    return $matches;
}

/**
 * @param array<string, Orbit> $map
 * @param array $orbits
 */
function mapOrbits(array &$map, array $orbits) {
    foreach ($orbits as $orbit) {
        list($_, $from, $to) = $orbit;

        if (!isset($map[$from])) {
            $map[$from] = new Orbit($from);
        }

        if (!isset($map[$to])) {
            $map[$to] = new Orbit($to);
        }

        $f_orb = &$map[$from];
        $t_orb = &$map[$to];

        $f_orb->addOrbit($t_orb);
        $t_orb->fromOrbit($f_orb);
    }
}

function countIndirectOrbits(Orbit &$orbit): int {
    $count = 1;

    foreach ($orbit->childOrbits as $orb) {
        $count += countIndirectOrbits($orb);
    }

    return $count;
}

function part1() {
    /** @var array<string, Orbit> $map */
    $map = [];
    $directOrbs = 0;
    $totalOrbs = 0;

    mapOrbits($map, getOrbits());

    foreach ($map as $name => $orbit) {
        $directOrbs += count($orbit->childOrbits);
        $totalOrbs += countIndirectOrbits($orbit);
    }

    printf("Part 1: %d\n", $totalOrbs - $directOrbs - 1);
}

part1();

function part2() {
    /** @var array<string, Orbit> $map */
    $map = [];
    mapOrbits($map, getOrbits());

    $distance = 0;
    $youPntr = &$map['YOU'];
    $youPath = [];
    $sanPntr = &$map['SAN'];
    $sanPath = [];

    while (true) {
        $youPath[] = $youCurr = $youPntr->parentOrbit->name;
        $youPntr = &$youPntr->parentOrbit;
        $sanPath[] = $sanCurr = $sanPntr->parentOrbit->name;
        $sanPntr = &$sanPntr->parentOrbit;

        foreach ($sanPath as $key => $orbit) {
            if (($pos = array_search($orbit, $youPath)) !== false) {
                $distance = $key + $pos;

                break 2;
            }
        }
    }

    printf("Part 2: %d\n", $distance);
}

part2();
