<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day17 extends Day
{
    protected const TITLE = 'Pyroclastic Flow';
    protected const MOVE_LEFT = '<';
    protected const MOVE_RIGHT = '>';

    protected const PART1_ITERATION = 2022;
    protected const PART2_ITERATION = 1000000000000;

    protected const SHAPES = [
        [
            [1, 1, 1, 1,]
        ],
        [
            [0, 1, 0,],
            [1, 1, 1,],
            [0, 1, 0,],
        ],
        [
            [0, 0, 1,],
            [0, 0, 1,],
            [1, 1, 1,],
        ],
        [
            [1,],
            [1,],
            [1,],
            [1,],
        ],
        [
            [1, 1,],
            [1, 1,],
        ]
    ];

    protected function loadData(): void
    {
        parent::loadData();
        $this->data = str_split($this->data[0], 1);
    }

    protected function part1(): Result
    {
        $answer = $this->getHeight(self::PART1_ITERATION);
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $answer = $this->getHeight(self::PART2_ITERATION);
        return new Result(Result::PART2, $answer);
    }

    protected function getHeight(int $desiredIterations): int
    {
        $cycles = 0;
        $cycleHeight = 0;
        $chamber = [];
        $shapeIndex = 0;
        $moveIndex = 0;
        $previousStates = [];
        $iterations = 0;
        for ($i = 0; $i < $desiredIterations; $i++) {
            $iterations++;
            $this->applyMovement($shapeIndex, $moveIndex, $chamber);
            $top = array_slice($chamber, 0, 10);
            $key = json_encode([$top, $shapeIndex, $moveIndex]);
            $shapeIndex = ($shapeIndex + 1) % count(self::SHAPES);

            // We've seen this state before, we're in a loop!
            if ($cycles === 0) {
                if (isset($previousStates[$key])) {
                    [$previousIteration, $previousHeight] = $previousStates[$key];
                    $cycleLength = $i - $previousIteration;
                    $cycleHeight = count($chamber) - $previousHeight;

                    $this->io->writeln("Identified cycle iterations <fg=yellow>{$previousIteration}</> to <fg=yellow>{$i}</> with height <fg=yellow>{$cycleHeight}</>");

                    $cycles = floor(($desiredIterations - $i) / $cycleLength);
                    $i += $cycles * $cycleLength;
                } else {
                    // Store this state to identify if we've looped around
                    $previousStates[$key] = [$i, count($chamber)];

                }
            }
        }
        $this->io->writeln("Calculated from <fg=yellow>{$cycles}</> cycles and <fg=yellow>{$iterations}</> iterations");
        return count($chamber) + ($cycles * $cycleHeight);
    }

    protected function applyMovement(int $shapeIndex, int &$moveIndex, array &$chamber): void
    {
        $shape = self::SHAPES[$shapeIndex];
        $top = (count($shape) * -1) - 3;
        $left = 2;
        while (true) {
            $move = $this->data[$moveIndex];
            $moveIndex = ($moveIndex + 1) % count($this->data);

            $canMoveLeft = true;
            $canMoveRight = true;
            foreach ($shape as $rY => $row) {
                foreach ($row as $rX => $column) {
                    if (!$column) {
                        continue;
                    }

                    // Check Left
                    if (isset($chamber[$top + $rY][$left + $rX - 1])) {
                        $canMoveLeft = false;
                    }
                    if (isset($chamber[$top + $rY][$left + $rX + 1])) {
                        $canMoveRight = false;
                    }
                    if (!$canMoveLeft && !$canMoveRight) {
                        break 2;
                    }
                }
            }

            if ($move === self::MOVE_LEFT && $canMoveLeft) {
                $left = max($left - 1, 0);
            } elseif ($move === self::MOVE_RIGHT && $canMoveRight) {
                $left = min($left + 1, 7 - count($shape[0]));
            }

            $canMoveDown = true;
            if ($top + count($shape) >= count($chamber)) {
                $canMoveDown = false;
            }
            foreach (array_reverse($shape, true) as $rY => $row) {
                foreach ($row as $rX => $column) {
                    if (!$column) {
                        continue;
                    }

                    if (isset($chamber[$top + $rY + 1][$rX + $left])) {
                        $canMoveDown = false;
                        break 2;
                    }
                }
            }
            // Compare top of chamber to bottom of our rock in current position
            if ($canMoveDown) {
                $top++;
            } else {
                foreach (array_reverse($shape, true) as $sY => $row) {
                    $y = $top + $sY;
                    if ($y < 0) {
                        array_unshift($chamber, []);
                        $y = 0;
                    }
                    foreach ($row as $sX => $value) {
                        if ($value) {
                            $chamber[$y][$left + $sX] = 1;
                        }
                    }
                }
                break;
            }
        }
    }
}