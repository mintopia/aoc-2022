<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day18 extends Day
{
    protected array $cubes = [];
    protected array $x = [];
    protected array $y = [];
    protected array $z = [];

    protected function loadData(): void
    {
        parent::loadData();
        $this->data = array_map(function(string $line): array
        {
            [$x, $y, $z] = explode(',', $line);

            if (!isset($this->cubes[$x])) {
                $this->cubes[$x] = [];
            }
            if (!isset($this->cubes[$x][$y])) {
                $this->cubes[$x][$y] = [];
            }
            $this->cubes[$x][$y][$z] = 1;

            $this->x[] = $x;
            $this->y[] = $y;
            $this->z[] = $z;

            return [$x, $y, $z];
        }, $this->data);
    }

    protected function part1(): Result
    {
        // Naive solution
        $exposedFaces = 0;
        foreach ($this->data as [$x, $y, $z]) {
            if (!isset($this->cubes[$x][$y][$z - 1])) {
                $exposedFaces++;
            }
            if (!isset($this->cubes[$x][$y][$z + 1])) {
                $exposedFaces++;
            }
            if (!isset($this->cubes[$x][$y - 1][$z])) {
                $exposedFaces++;
            }
            if (!isset($this->cubes[$x][$y - 1][$z])) {
                $exposedFaces++;
            }
            if (!isset($this->cubes[$x - 1][$y][$z])) {
                $exposedFaces++;
            }
            if (!isset($this->cubes[$x + 1][$y][$z])) {
                $exposedFaces++;
            }
        }
        return new Result(Result::PART1, $exposedFaces);
    }

    protected function part2(Result $part1): Result
    {
        // 3D Flood Fill

        $xMax = max(...$this->x) + 1;
        $yMax = max(...$this->y) + 1;
        $zMax = max(...$this->z) + 1;

        $exposedFaces = 0;
        $queue = new \SplQueue;
        $queue->enqueue([-1, -1, -1]);
        $visited = [
            [-1, -1, -1],
        ];
        while (!$queue->isEmpty()) {
            $point = $queue->dequeue();
            [$x, $y, $z] = $point;

            // Check our boundaries
            if ($x > $xMax || $y > $yMax || $z > $zMax) {
                continue;
            }
            if ($x < -1 || $y < -1 || $z < -1) {
                continue;
            }

            // Expand out in each direction
            foreach ([$x - 1, $x + 1] as $nX) {
                $exposedFaces += $this->addNeighbour($queue, $visited, $nX, $y, $z);
            }
            foreach ([$y - 1, $y + 1] as $nY) {
                $exposedFaces += $this->addNeighbour($queue, $visited, $x, $nY, $z);
            }
            foreach ([$z - 1, $z + 1] as $nZ) {
                $exposedFaces += $this->addNeighbour($queue, $visited, $x, $y, $nZ);
            }
        }

        return new Result(Result::PART2, $exposedFaces);
    }

    protected function addNeighbour(\SplQueue $queue, array &$visited, int $x, int $y, int $z)
    {
        // Have we visited this location?
        if (in_array([$x, $y, $z], $visited)) {
            return 0;
        }

        // Does this location contain a cube? If so, we have an exposed face
        if (isset($this->cubes[$x][$y][$z])) {
            return 1;
        }

        $visited[] = [$x, $y, $z];
        $queue->enqueue([$x, $y, $z]);
        return 0;
    }
}