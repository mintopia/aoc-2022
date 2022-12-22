<?php

namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Day22\Direction;
use Mintopia\Aoc2022\Helpers\Result;

class Day22 extends Day
{
    protected const TITLE = 'Monkey Map';

    protected array $instructions = [];
    protected array $map = [];

    protected const OPEN = '.';
    protected const WALL = '#';

    protected function loadData(): void
    {
        $input = $this->getInputFile();
        [$map, $instructions] = explode("\n\n", $input);
        foreach (explode("\n", $map) as $row => $line) {
            $this->map[$row] = [];
            foreach (str_split($line) as $column => $char) {
                if ($char === self::OPEN) {
                    $this->map[$row][$column] = self::OPEN;
                } elseif ($char === self::WALL) {
                    $this->map[$row][$column] = self::WALL;
                }
            }
        }

        $buffer = "";
        foreach (str_split($instructions) as $char) {
            if (in_array($char, ['L', 'R'])) {
                if ($buffer) {
                    $this->instructions[] = (int)$buffer;
                    $buffer = '';
                }
                $this->instructions[] = $char;
            } else {
                $buffer .= $char;
            }
        }
        if ($buffer) {
            $this->instructions[] = (int)$buffer;
        }
    }

    protected function part1(): Result
    {
        $answer = $this->navigateMap([$this, 'part1Movement']);
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $answer = $this->navigateMap([$this, 'part2Movement']);
        return new Result(Result::PART2, $answer);
    }

    protected function navigateMap(callable $movementFn): int
    {
        $direction = Direction::Right;
        $position = [0, 0];
        foreach ($this->map[0] as $col => $space) {
            if ($space === self::OPEN) {
                $position = [$col, 0];
                break;
            }
        }

        foreach ($this->instructions as $instruction) {
            if ($instruction === 'L') {
                $direction = Direction::from(($direction->value + 3) % 4);
                continue;
            }
            if ($instruction === 'R') {
                $direction = Direction::from(($direction->value + 5) % 4);
                continue;
            }

            [$position, $direction] = $movementFn($position, $direction, (int)$instruction);
        }

        return (1000 * ($position[1] + 1)) + (4 * ($position[0] + 1)) + $direction->value;
    }

    protected function part1Movement(array $position, Direction $direction, int $instruction): array
    {
        // Apply movement
        for ($i = 0; $i < $instruction; $i++) {
            [$x, $y] = $position;
            switch ($direction) {
                case Direction::Left:
                    $x--;
                    if (!isset($this->map[$y][$x])) {
                        $keys = array_keys($this->map[$y]);
                        $x = end($keys);
                    }
                    break;

                case Direction::Right:
                    $x++;
                    if (!isset($this->map[$y][$x])) {
                        $keys = array_keys($this->map[$y]);
                        $x = $keys[0];
                    }
                    break;

                case Direction::Up:
                    $y--;
                    if (!isset($this->map[$y][$x])) {
                        $y = count($this->map) - 1;
                        while (!isset($this->map[$y][$x])) {
                            $y--;
                        }
                    }
                    break;

                case Direction::Down:
                    $y++;
                    if (!isset($this->map[$y][$x])) {
                        $y = 0;
                        while (!isset($this->map[$y][$x])) {
                            $y++;
                        }
                    }
                    break;
            }

            if ($this->map[$y][$x] == self::OPEN) {
                $position = [$x, $y];
                echo "{$x},{$y}\r\n";
            } else {
                break;
            }
        }
        return [$position, $direction];
    }

    protected function part2Movement(array $position, Direction $direction, int $instruction): array
    {
        echo "I: {$instruction}\r\n";
        echo "D: {$direction->name}\r\n";
        $faceSize = 50;
        if ($this->input->getOption('test')) {
            $faceSize = 4;
        }
        // Apply movement
        for ($i = 0; $i < $instruction; $i++) {
            [$x, $y] = $position;
            $originalDirection = $direction;
            switch ($direction) {
                case Direction::Left:
                    if (isset($this->map[$y][$x - 1])) {
                        $x--;
                        break;
                    }

                    if ($y < $faceSize) {
                        // Move to Face 3
                        $direction = Direction::Down;
                        $x = $faceSize + $y;
                        $y = $faceSize;
                    } elseif ($y < $faceSize * 2) {
                        // Move to Face 6
                        $direction = Direction::Up;
                        $x = ($faceSize * 4) - ($y - $faceSize);
                        $y = ($faceSize * 3) - 1;
                    } else {
                        // Moving to Face 3
                        $direction = Direction::Up;
                        $y = ($faceSize * 2) - 1;
                        $x = ($faceSize * 2) - ($x - ($faceSize * 2));
                    }
                    break;

                case Direction::Right:
                    if (isset($this->map[$y][$x + 1])) {
                        $x++;
                        break;
                    }
                    if ($y < $faceSize) {
                        // Moving onto Face 6
                        $direction = Direction::Left;
                        $x = (4 * $faceSize) - 1;
                        $y = (3 * $faceSize) - 1 - $y - 1;
                    } elseif ($y < ($faceSize * 2)) {
                        // Moving onto Face 6
                        $direction = Direction::Down;
                        $x = ($faceSize * 4) - 1 - ($y - $faceSize);
                        $y = $faceSize * 2;
                    } else {
                        // Moving onto Face 1
                        $direction = Direction::Left;
                        $x = (3 * $faceSize) - 1;
                        $y = $faceSize - ($y - (2 * $faceSize));
                    }
                    break;

                case Direction::Up:
                    if (isset($this->map[$y - 1][$x])) {
                        $y--;
                        break;
                    }
                    if ($x < $faceSize) {
                        // Face 1
                        $direction = Direction::Down;
                        $y = 0;
                        $x = (3 * $faceSize) - $x;
                    } elseif ($x < $faceSize * 2) {
                        // Face 1
                        $direction = Direction::Right;
                        $y = $x - $faceSize;
                        $x = $faceSize * 2;
                    } elseif ($x < $faceSize * 3) {
                        // Face 2
                        $direction = Direction::Down;
                        $y = $faceSize;
                        $x = $faceSize - ($x - ($faceSize * 2)) - 1;
                    } else {
                        // Going onto Face 4
                        $direction = Direction::Left;
                        $y = ($faceSize * 2) - ($x - 3 * $faceSize);
                        $x = (3 * $faceSize) - 1;
                    }
                    break;

                case Direction::Down:
                    if (isset($this->map[$y + 1][$x])) {
                        $y++;
                        break;
                    }
                    if ($x < $faceSize) {
                        // Face 5
                        $direction = Direction::Up;
                        $y = ($faceSize * 3) - 1;
                        $x = ($faceSize * 3) - $x - 1;
                    } elseif ($x < ($faceSize * 2)) {
                        // Face 5
                        $direction = Direction::Right;
                        $y = (($faceSize * 2) - $x) + ($faceSize * 2) - 1;
                        $x = $faceSize * 2;
                    } elseif ($x < ($faceSize * 3)) {
                        // Face 2
                        $direction = Direction::Up;
                        $x = ($faceSize * 3) - 1 - $x;
                        $y = ($faceSize * 2) - 1;
                    } else {
                        // Face 2
                        $direction = Direction::Right;
                        $y = $faceSize + (($faceSize * 4) - 1);
                        $x = 0;
                    }
                    break;
            }

            if (!isset($this->map[$y][$x])) {
                var_dump($x);
                var_dump($y);
                var_dump($position);
                var_dump($originalDirection->name);
                var_dump($direction->name);
                die();
            }

            if ($this->map[$y][$x] == self::OPEN) {
                $position = [$x, $y];
                echo "{$x},{$y}\r\n";
            } else {
                // We didn't move, so don't do the transition
                $direction = $originalDirection;
            }
        }
        return [$position, $direction];
    }
}