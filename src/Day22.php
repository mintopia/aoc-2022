<?php

namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day22 extends Day
{
    protected const TITLE = 'Monkey Map';

    protected array $instructions = [];
    protected array $map = [];

    protected const OPEN = '.';
    protected const WALL = '#';

    protected const DIRECTIONS = [
        [0, 1], [1, 0], [0, -1], [-1, 0],
    ];

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
        // Why is test and real input different format? Who knows?
        $borders = [
            [[1, 0], [2, 1], [1, 2], [4, 3]],
            [[0, 0], [1, 1], [0, 2], [1, 3]],
            [[2, 0], [4, 1], [2, 2], [0, 3]],
            [[4, 0], [5, 1], [4, 2], [5, 3]],
            [[3, 0], [0, 1], [3, 2], [2, 3]],
            [[5, 0], [3, 1], [5, 2], [3, 3]],
        ];
        $offset = [
            [0, 1], [0, 2], [1, 1], [2, 0], [2, 1], [3, 0],
        ];
        $scale = 50;
        if (count($this->map) <= 12) {
            $borders = [
                [[0, 0], [3, 1], [0, 2], [4, 3]],
                [[2, 0], [1, 1], [3, 2], [1, 3]],
                [[3, 0], [2, 1], [1, 2], [2, 3]],
                [[1, 0], [4, 1], [2, 2], [0, 3]],
                [[5, 0], [0, 1], [5, 2], [3, 3]],
                [[4, 0], [5, 1], [4, 2], [5, 3]],
            ];
            $offset = [
                [0, 2], [1, 0], [1, 1], [1, 2], [2, 2], [2, 3],
            ];
            $scale = 4;
        }
        $answer = $this->solveMap($scale, $offset, $borders);
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $borders = [
            [[1, 0], [2, 1], [3, 0], [5, 0]],
            [[4, 2], [2, 2], [0, 2], [5, 3]],
            [[1, 3], [4, 1], [3, 1], [0, 3]],
            [[4, 0], [5, 1], [0, 0], [2, 0]],
            [[1, 2], [5, 2], [3, 2], [2, 3]],
            [[4, 3], [1, 1], [0, 1], [3, 3]],
        ];
        $offset = [
            [0, 1], [0, 2], [1, 1], [2, 0], [2, 1], [3, 0],
        ];
        $scale = 50;
        if (count($this->map) <= 12) {
            $borders = [
                [[5, 2], [3, 1], [2, 1], [1, 1]],
                [[2, 0], [4, 3], [5, 3], [0, 1]],
                [[3, 0], [4, 0], [1, 2], [0, 0]],
                [[5, 1], [4, 1], [2, 2], [0, 3]],
                [[5, 0], [1, 3], [2, 3], [3, 3]],
                [[0, 2], [1, 0], [4, 2], [3, 2]],
            ];
            $offset = [
                [0, 2], [1, 0], [1, 1], [1, 2], [2, 2], [2, 3],
            ];
            $scale = 4;
        }
        $answer = $this->solveMap($scale, $offset, $borders);
        return new Result(Result::PART2, $answer);
    }

    protected function solveMap(int $scale, array $offsets, array $borders): int
    {
        $faces = [];
        for ($faceNumber = 0; $faceNumber < 6; $faceNumber++) {
            $face = [];
            for ($row = 0; $row < $scale; $row++) {
                $face[$row] = [];
                for ($col = 0; $col < $scale; $col++) {
                    $row2 = $offsets[$faceNumber][0] * $scale + $row;
                    $col2 = $offsets[$faceNumber][1] * $scale + $col;
                    $face[$row][$col] = $this->map[$row2][$col2];
                }
            }
            $faces[] = $face;
        }

        $currentFaceNumber = 0;
        $currentRow = 0;
        $currentCol = array_search(self::OPEN, $faces[$currentFaceNumber][0]);
        $currentDirection = 0;

        foreach ($this->instructions as $instruction) {
            if ($instruction === 'L') {
                $currentDirection = ($currentDirection + 3) % 4;
                continue;
            }
            if ($instruction === 'R') {
                $currentDirection = ($currentDirection + 1) % 4;
                continue;
            }
            for ($i = 0; $i < $instruction; $i++) {
                $newRow = $currentRow + self::DIRECTIONS[$currentDirection][0];
                $newCol = $currentCol + self::DIRECTIONS[$currentDirection][1];
                if ($newRow >= 0 && $newRow < $scale && $newCol >= 0 && $newCol < $scale) {
                    // No wrapping
                    if ($faces[$currentFaceNumber][$newRow][$newCol] === self::WALL) {
                        break;
                    }
                    $currentRow = $newRow;
                    $currentCol = $newCol;
                    continue;
                }

                // We're now wrapping - get our new face and direction
                [$newFaceNumber, $newDirection] = $borders[$currentFaceNumber][$currentDirection];

                $row3 = [$newRow, $scale - 1 - $newCol, $scale - 1 - $newRow, $newCol][$currentDirection];
                $col3 = [$scale - 1 - $newRow, $newCol, $newRow, $scale - 1 - $newCol][$currentDirection];

                $newRow = [$row3, 0, $scale - 1 - $row3, $scale - 1][$newDirection];
                $newCol = [0, $col3, $scale - 1, $scale - 1 - $col3][$newDirection];

                if ($faces[$newFaceNumber][$newRow][$newCol] === self::WALL) {
                    break;
                }
                $currentFaceNumber = $newFaceNumber;
                $currentDirection = $newDirection;
                $currentRow = $newRow;
                $currentCol = $newCol;
            }
        }

        $row = $offsets[$currentFaceNumber][0] * $scale + $currentRow;
        $col = $offsets[$currentFaceNumber][1] * $scale + $currentCol;
        return (($row + 1) * 1000) + (($col + 1) * 4) + $currentDirection;
    }
}