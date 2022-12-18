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

            // Create a lazy-filled multidimensional array of the cubes indexed by X,Y,Z
            if (!isset($this->cubes[$x])) {
                $this->cubes[$x] = [];
            }
            if (!isset($this->cubes[$x][$y])) {
                $this->cubes[$x][$y] = [];
            }
            $this->cubes[$x][$y][$z] = 1;

            // And for easy of use, put them into simple arrays
            $this->x[] = $x;
            $this->y[] = $y;
            $this->z[] = $z;

            return [$x, $y, $z];
        }, $this->data);
    }

    protected function part1(): Result
    {
        // Iterate each cube and check if its neighbour exists in our cubes array
        $exposedFaces = 0;
        foreach ($this->data as [$x, $y, $z]) {
            foreach ([-1, 1] as $i) {
                $exposedFaces += (int) !isset($this->cubes[$x + $i][$y][$z]);
                $exposedFaces += (int) !isset($this->cubes[$x][$y + $i][$z]);
                $exposedFaces += (int) !isset($this->cubes[$x][$y][$z + $i]);
            }
        }
        return new Result(Result::PART1, $exposedFaces);
    }

    protected function part2(Result $part1): Result
    {
        // 3D Flood Fill from a point at the edge of the volume
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
            foreach ([-1, 1] as $i) {
                $exposedFaces += $this->addNeighbour($queue, $visited, $x + $i, $y, $z);
                $exposedFaces += $this->addNeighbour($queue, $visited, $x, $y + $i, $z);
                $exposedFaces += $this->addNeighbour($queue, $visited, $x, $y, $z + $i);
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

        // We've visited this location, don't use it again
        $visited[] = [$x, $y, $z];
        $queue->enqueue([$x, $y, $z]);
        return 0;
    }
}