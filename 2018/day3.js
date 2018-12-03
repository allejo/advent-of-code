const fs = require('fs');

function createGrid(length) {
    const grid = [];

    for (let i = 0; i < length; i++) {
        grid.push(Array(length));
    }

    return grid;
}

function parse(str) {
    const matches = str.match(/#(\d+) @ (\d+),(\d+): (\d+)x(\d+)/);

    return {
        id: +matches[1],
        top: +matches[3],
        left: +matches[2],
        width: +matches[4],
        height: +matches[5],
    };
}

function main(str, gridSize) {
    const plans = str.split("\n");
    const grid = createGrid(gridSize);
    const noOverlaps = [];
    let dupes = 0;

    for (let i = 0; i < plans.length; i++) {
        const plan = parse(plans[i]);

        noOverlaps.push(plan.id);

        for (let row = 0; row < plan.width; row++) {
            for (let col = 0; col < plan.height; col++) {
                const cv = grid[plan.left + row][plan.top + col];

                // This cell has never been visited before, so create an array
                // in this cell with an array of only this plan
                if (typeof cv === 'undefined') {
                    grid[plan.left + row][plan.top + col] = [plan.id];
                    continue;
                }

                // Only count duplicates once. If the length is one, then that
                // means it's been visited once and only once.
                //
                // If it's been visited more than once, then we already counted
                // it as a duplicate so don't need to double count it.
                if (cv.length === 1) {
                    dupes++;
                }
                
                // The current index overlaps, so remove it
                let cIdx = noOverlaps.indexOf(plan.id);
                if (cIdx > -1) noOverlaps.splice(cIdx, 1);

                // Keep a list of all overlapping cuts; and be sure to remove
                // all of them from the `noOverlaps` array.
                grid[plan.left + row][plan.top + col].push(plan.id);
                cv.forEach(element => {
                    let x = noOverlaps.indexOf(element);

                    if (x > -1) noOverlaps.splice(x, 1);
                });
            }
        }
    }

    console.log('Part 1:', dupes);
    console.log('Part 2:', noOverlaps);
}

const sampleInput = fs.readFileSync('day3.input.sample.txt', 'utf-8');
main(sampleInput, 8);

console.log('---');

const realInput = fs.readFileSync('day3.input.txt', 'utf-8');
main(realInput, 1000);
