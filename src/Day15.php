<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day15 extends Day
{
    protected const TITLE = 'Beacon Exclusion Zone';
    protected array $sensors = [];
    protected array $beacons = [];

    protected int $minX = PHP_INT_MAX;
    protected int $maxX = PHP_INT_MIN;


    protected function loadData(): void
    {
        parent::loadData();
        $data = [];
        foreach ($this->data as $line) {
            preg_match('/^Sensor at x=(?<sx>\-?\d+), y=(?<sy>\-?\d+).*x=(?<bx>\-?\d+), y=(?<by>\-?\d+)$/', $line, $matches);
            $sensor = [(int)$matches['sx'], (int)$matches['sy']];
            $beacon = [(int)$matches['bx'], (int)$matches['by']];

            $distance = $this->getManhattanDistance($sensor, $beacon);

            $this->sensors[] = (object) [
                'x' => $sensor[0],
                'y' => $sensor[1],
                'distance' => $distance,
            ];

            if (!in_array($beacon, $this->beacons)) {
                $this->beacons[] = $beacon;
            }

            $this->minX = min($this->minX, $beacon[0], $sensor[0] - $distance);
            $this->maxX = max($this->maxX, $beacon[0], $sensor[0] + $distance);
        }
        $this->data = $data;
    }

    protected function getManhattanDistance(array $one, array $two): int
    {
        return abs($one[0] - $two[0]) + abs($one[1] - $two[1]);
    }

    protected function part1(): Result
    {
        $y = $this->input->getOption('test') ? 10 : 2000000;

        $answer = 0;
        for ($x = $this->minX; $x <= $this->maxX; $x++) {
            foreach ($this->sensors as $sensor) {
                $distance = $this->getManhattanDistance([$sensor->x, $sensor->y], [$x, $y]);
                // If sensor is out of range, ignore
                if ($distance > $sensor->distance) {
                    continue;
                }

                // This X,Y is in range of a sensor, it's likely the sensor covers more of our row
                // Get distance from sensor to our row, subtract it from the distance
                $verticalDistance = $this->getManhattanDistance([$sensor->x, $y], [$sensor->x, $sensor->y]);
                $remaining = $sensor->distance - $verticalDistance;
                $newX = $sensor->x + $remaining;

                // Answer is increased by amount we've skipped
                $answer += ($newX - $x) + 1;
                $x = $newX;
                break;
            }
        }
        foreach ($this->beacons as [$bx, $by]) {
            if ($by == $y) {
                $answer--;
            }
        }
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        [$x, $y] = $this->getBeaconLocation();
        $answer = (4000000 * $x) + $y;
        return new Result(Result::PART2, $answer);
    }

    protected function getBeaconLocation(): array
    {
        // This is just a brute force of our strategy from part 1, I'm not proud, but it works.
        $max = $this->input->getOption('test') ? 20 : 4000000;
        for ($y = 0; $y <= $max; $y++) {
            for ($x = 0; $x <= $max; $x++) {
                $current = [$x, $y];
                if (in_array($current, $this->beacons)) {
                    continue;
                }
                foreach ($this->sensors as $sensor) {
                    $distance = $this->getManhattanDistance([$sensor->x, $sensor->y], [$x, $y]);
                    // Sensor is out of range - our space could be empty if no other sensor is in range
                    if ($distance > $sensor->distance) {
                        continue;
                    }

                    // This X,Y is in range of a sensor, it's likely the sensor covers more of our row
                    // Get distance from sensor to our row, subtract it from the distance
                    $verticalDistance = $this->getManhattanDistance([$sensor->x, $y], [$sensor->x, $sensor->y]);
                    $remaining = $sensor->distance - $verticalDistance;
                    $x = $sensor->x + $remaining;
                    if ($x > $max) {
                        break;
                    }
                }
                if ($x === $current[0]) {
                    return $current;
                }
            }
        }
        throw new \Exception('Unable to find beacon location');
    }
}