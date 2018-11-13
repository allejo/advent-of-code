const directions = ['North', 'East', 'West', 'South'];

function getNewDirection(curr, right) {
    switch(curr) {
        case 0:
            return right ? 1 : 2;

        case 1:
            return right ? 3 : 0;

        case 2:
            return right ? 0 : 3;

        case 3:
            return right ? 2 : 1;
    }
}

function buildRange(coor1, coor2) {
    // The coordinate that is changing (x = 0; y = 1)
    const coor = +(coor1[0] === coor2[0]);
    
    const start = Math.min(coor1[coor], coor2[coor]);
    const end = Math.max(coor1[coor], coor2[coor]);
    let range = [];

    for (let i = start; i <= end; i++) {
        let p = [];
        p[coor] = i;
        p[+!coor] = coor1[+!coor];

        range.push(p);
    }

    if (coor1[coor] > coor2[coor]) {
        range = range.reverse();
    }

    return range.slice(1);
}

function calculateDistance(steps_raw) {
    const visitedLocations = new Set([
        String([0, 0]),
    ]);
    const steps = steps_raw.replace(/,/g, '').split(' ');

    // 0 - North
    // 1 - East
    // 2 - West
    // 3 - South
    let direction = 0;
    let currPos = [0, 0];
    let overlap = null;

    for (let i = 0; i < steps.length; i++) {
        const step = steps[i];

        const turnRight = (step[0] === 'R');
        const oldPos = currPos.slice();
        const count = +step.substr(1);

        direction = getNewDirection(direction, turnRight);

        switch (direction) {
            case 0:
                currPos[1] += count;
                break;

            case 3:
                currPos[1] -= count;
                break;

            case 1:
                currPos[0] += count;
                break;

            case 2:
                currPos[0] -= count;
                break;
        }

        if (overlap === null) {
            let range = buildRange(oldPos, currPos);

            for (let j = 0; j < range.length; j++) {
                let v = String(range[j]);

                if (visitedLocations.has(v)) {
                    overlap = v;
                    break;
                }

                visitedLocations.add(v);
            }
        }
    }

    const intersect = overlap.split(',');

    return {
        distance: Math.abs(currPos[0]) + Math.abs(currPos[1]),
        overlap: Math.abs(+intersect[0]) + Math.abs(+intersect[1]),
    };
}

const expected = {
    'R2, L3': 5,
    'R2, R2, R2': 2,
    'R5, L5, R5, R3': 12,
};

const steps_raw = 'R3, L5, R2, L2, R1, L3, R1, R3, L4, R3, L1, L1, R1, L3, R2, L3, L2, R1, R1, L1, R4, L1, L4, R3, L2, L2, R1, L1, R5, R4, R2, L5, L2, R5, R5, L2, R3, R1, R1, L3, R1, L4, L4, L190, L5, L2, R4, L5, R4, R5, L4, R1, R2, L5, R50, L2, R1, R73, R1, L2, R191, R2, L4, R1, L5, L5, R5, L3, L5, L4, R4, R5, L4, R4, R4, R5, L2, L5, R3, L4, L4, L5, R2, R2, R2, R4, L3, R4, R5, L3, R5, L2, R3, L1, R2, R2, L3, L1, R5, L3, L5, R2, R4, R1, L1, L5, R3, R2, L3, L4, L5, L1, R3, L5, L2, R2, L3, L4, L1, R1, R4, R2, R2, R4, R2, R2, L3, L3, L4, R4, L4, L4, R1, L4, L4, R1, L2, R5, R2, R3, R3, L2, L5, R3, L3, R5, L2, R3, R2, L4, L3, L1, R2, L2, L3, L5, R3, L1, L3, L4, L3';

console.log(calculateDistance(steps_raw));
// console.log(calculateDistance('R8, R4, R4, R8'));
