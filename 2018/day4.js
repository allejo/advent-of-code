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

function findSleepiestGuard(actions) {
    let napRecord = {};

    for (let i = 0; i < actions.length; i++) {
        const action = actions[i];

        if (action.action === SHIFT_START) {
            continue;
        }

        if (!napRecord[action.id]) {
            napRecord[action.id] = {
                st: 0,
            };
        }

        if (action.action === WAKES_UP) {
            napRecord[action.id].st += action.sleep_time;
        }
    }

    const sleepiestGuard = Object.keys(napRecord).reduce((a, b) => napRecord[a].st > napRecord[b].st ? a : b);

    return {
        id: sleepiestGuard,
        time: napRecord[sleepiestGuard],
    };
}

const sample = fs.readFileSync('day4.input.sample.txt', 'utf-8');
const sleepActions = parse(sample);

console.log(sleepActions);
console.log(findSleepiestGuard(sleepActions));
