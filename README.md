# Advent of Code 2022
An attempt at 2022 Advent of Code in PHP, until I get bored/run out of time around day 8 or 9 and stop. In 2021, I managed to make it to Day 23 before family commitments stopped me continuing!

This AOC application is [shamelessly copied/forked from my 2021 version](https://github.com/mintopia/aoc-2021).

## Installation

This package is meant to be used with PHP 8.1 with dependencies managed by composer. If you have PHP 8.1 and composer installed, you can install the dependencies using:

```bash
composer install
```

If you don't have PHP 8.1 and composer but do have Docker and Docker compose, you can use the development docker containers:

```bash
docker-compose run --rm composer install
```

This will install the relevant dependencies.

## Usage

Once you have installed dependencies, if you're running PHP natively, you can use `php aoc` or `./aoc`' to see the available commands

```bash
php aoc list
```

The days can then be run using:

```bash
php aoc day1
```

If you are using the docker container approach, you can run these commands in the following way:

```bash
docker-compose run --rm aoc list
docker-compose run --rm aoc day1
```

### Creating a New Day

It's 5AM UK time, it's time for another day of Advent of Code. Before starting to solve the problems of these elves, you can create a skeleton new day with the following command:

```bash
php aoc make:day <number>
```

Or

```bash
docker-compose run --rm aoc make:day <number>
```

It will copy the Skeleton day class and inputs/outputs to the right places. If the files already exist, it will warn you and ask if you want to overwrite them.

### Test Data

To run the day with testdata, pass `--test` to the command. It will use test fixtures and answers from the `testdata` directory.

### Benchmarking

Benchmarking mode allows you to measure performance of the day's task. It will run the task repeatedly and show the average duration of part1, part2 and data loading.

You can enable benchmarking mode by passing `--benchmark` to the command. In benchmarking mode, most days will not output any visualisation to increase performance.

The number of iterations is configurable with `--iterations={number}`. The default is 100.

```bash
php aoc day16 --benchmark
```
```
Advent of Code: Day 16

Benchmarking: 100 iterations
============================

Performance
===========

 -------------- -----------
  Section        Time (ms)
 -------------- -----------
  Data Loading   4.322
  Part 1         0.529
  Part 2         0.601
 -------------- -----------
  Total          5.452
 -------------- -----------
```

## License

MIT License

Copyright (c) 2022 Jessica Smith

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

