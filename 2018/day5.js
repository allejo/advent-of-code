const fs = require('fs');

function removeReactionIteration(str) {
    const chars = str.split('');
    let hasPerformedAction = false;

    for (let i = 1; i < chars.length; i++) {
        const curr = chars[i];
        const prev = chars[i - 1];

        if (curr.toLowerCase() === prev.toLowerCase() && curr !== prev) {
            hasPerformedAction = true;
            chars[i] = '_';
            chars[i - 1] = '_';
        }
    }

    return {
        touched: hasPerformedAction,
        final: chars.join('').replace(/_/g, ''),
    };
}

function removeReactions(str) {
    let w = str;

    while (true) {
        let res = removeReactionIteration(w);

        if (!res.touched) {
            return res.final;
        }

        w = res.final;
    }
}

function part1(str) {
    return removeReactions(str).length;
}

function indexOfMin(arr) {
    if (arr.length === 0) {
        return -1;
    }

    let minIndex = 0;
    let currMin = arr[minIndex];

    for (let i = 1; i < arr.length; i++) {
        if (typeof currMin === 'undefined' || arr[i] < currMin) {
            currMin = arr[i];
            minIndex = i;
        }
    }

    return minIndex;
}

function part2(str) {
    const lowerA = 97;
    const counts = [];

    for (let i = 0; i < 26; i++) {
        const letter = String.fromCharCode(lowerA + i);
        const re = new RegExp(letter, 'ig');

        const trialStr = str.replace(re, '');
        counts[i] = part1(trialStr);
    }

    return counts[indexOfMin(counts)];
}

const fileContent = fs.readFileSync(__dirname + '/day5.input.txt', 'utf-8').trim();

console.log(part1(fileContent));
console.log(part2(fileContent));
