const fs = require('fs');

const sample = ["abcdef", "bababc", "abbcde", "abcccd", "aabcdd", "abcdee", "ababab"];
const input = fs.readFileSync('day2.input.txt', 'utf-8').split("\n");

function part1(list) {
    let cnt = [0, 0]
    
    for (let i = 0; i < list.length; i++) {
        const res = list[i].split('').sort().join('').match(/(\w)\1{1,}/g);
        const fxn = len => e => e.length === len;

        if (!res) continue;

        cnt[0] += +res.some(fxn(2));
        cnt[1] += +res.some(fxn(3));
    }

    return cnt.reduce((p, v) => p * v);
}

console.log(part1(sample), '=', 12);
console.log('Part 1:', part1(input));

// Part 2

String.prototype.diff = function(a) {
    let pos = [], diff = this.split('').filter((v, i) => {
        return (v !== a[i]) ? pos.push(i) : 0;
    });

    return { pos, diff };
};

function part2(list) {
    for (let i = 0; i < list.length; i++) {
        for (let j = 0; j < list.length; j++) {
            const diff = list[i].diff(list[j]);
            
            if (diff.pos.length === 1) {
                return list[i].slice(0, diff.pos[0]) + list[i].slice(diff.pos[0] + 1);
            }
        }
    }
}

console.log('Part 2:', part2(input));
