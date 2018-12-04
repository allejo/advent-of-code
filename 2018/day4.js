const fs = require('fs');

const SHIFT_START = 0;
const FALL_ASLEEP = 1;
const WAKES_UP = 2;

function castAction(str) {
    if (str === 'wakes up') {
        return WAKES_UP;
    }

    if (str === 'falls asleep') {
        return FALL_ASLEEP;
    }

    return SHIFT_START;
}

function indexOfMax(arr) {
    if (arr.length === 0) {
        return -1;
    }

    let maxIndex = 0;
    let currMax = arr[maxIndex];

    for (let i = 1; i < arr.length; i++) {
        if (typeof currMax === 'undefined' || arr[i] > currMax) {
            currMax = arr[i];
            maxIndex = i;
        }
    }

    return maxIndex;
}

function parse(input) {
    const regex = /\[((\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}))] (Guard #(\d+).+|wakes up|falls asleep)/;
    const lines = input.split('\n');
    let actions = [];

    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];

        if (!line.trim()) {
            continue;
        }

        const matches = line.match(regex);
        const action = matches[7];

        actions.push({
            date_raw: matches[1],
            date: {
                year: +matches[2],
                month: +matches[3],
                day: +matches[4],
                hour: +matches[5],
                mins: +matches[6],
            },
            id: castAction(action) === SHIFT_START ? +matches[8] : null,
            action: castAction(action),
        });
    }

    actions.sort((a, b) => {
        if(a.date_raw < b.date_raw) return -1;
        if(a.date_raw > b.date_raw) return 1;
        return 0;
    });

    for (let i = 0; i < actions.length; i++) {
        const action = actions[i];

        if (action.action !== SHIFT_START) {
            action.id = actions[i - 1].id;
        }

        // For how long did the guard sleep?
        if (action.action === WAKES_UP) {
            const prev = actions[i - 1];

            action.sleep_time = ((action.date.hour * 60) + action.date.mins) - ((prev.date.hour * 60) + prev.date.mins);
        }
    }

    return actions;
}

function buildGuardSleepMap(actions) {
    let napRecord = {};

    for (let i = 0; i < actions.length; i++) {
        const action = actions[i];

        if (action.action === SHIFT_START) {
            continue;
        }

        if (!napRecord[action.id]) {
            napRecord[action.id] = {
                id: action.id,
                minutes: {},
                st: 0,
            };
        }

        const minutesKey = `${action.date.month}-${action.date.day}`;
        let minutesArray = napRecord[action.id].minutes[minutesKey];

        if (action.action === FALL_ASLEEP) {
            if (!minutesArray) {
                napRecord[action.id].minutes[minutesKey] = [];
            }

            napRecord[action.id].minutes[minutesKey].push([action.date.mins]);
        }

        if (action.action === WAKES_UP) {
            napRecord[action.id].minutes[minutesKey][minutesArray.length - 1][1] = action.date.mins - 1;
            napRecord[action.id].st += action.sleep_time;
        }
    }

    return napRecord;
}

function objectWithHighestValueByKey(obj, key) {
    return Object.keys(obj).reduce((a, b) => obj[a][key] > obj[b][key] ? a : b);
}

function findSleepiestGuard(napRecords) {
    const sleepiestGuard = objectWithHighestValueByKey(napRecords, 'st');

    return napRecords[sleepiestGuard];
}

function findMostCommonMinuteAsleep(guardInfo) {
    const minutesAsleep = guardInfo.minutes;
    const mcmTally = Array(60);

    for (let m in minutesAsleep) {
        const minutes = minutesAsleep[m];

        for (let i = 0; i < minutes.length; i++) {
            const start = minutes[i][0];
            const end = minutes[i][1];

            for (let j = start; j <= end; j++) {
                if (!mcmTally[j]) {
                    mcmTally[j] = 0;
                }

                mcmTally[j] += 1;
            }
        }
    }

    const idx = indexOfMax(mcmTally);

    return {
        count: mcmTally[idx],
        minute: idx,
    };
}

function part1(napRecords) {
    const sleepiestGuard = findSleepiestGuard(napRecords);
    const mostCommonMinute = findMostCommonMinuteAsleep(sleepiestGuard);

    return sleepiestGuard.id * mostCommonMinute.minute;
}

function part2(napRecords) {
    const guardNapMapping = {};

    for (let guardID in napRecords) {
        const record = napRecords[guardID];
        const sleepiestMinute = findMostCommonMinuteAsleep(record);

        guardNapMapping[guardID] = sleepiestMinute;
    }

    const mostConsistentGuard = objectWithHighestValueByKey(guardNapMapping, 'count');
    
    return +mostConsistentGuard * guardNapMapping[mostConsistentGuard].minute;
}

const sample = fs.readFileSync(__dirname + '/day4.input.txt', 'utf-8');
const sleepMap = buildGuardSleepMap(parse(sample));

console.log('Part 1:', part1(sleepMap));
console.log('Part 2:', part2(sleepMap));
