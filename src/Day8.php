<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day8 extends Day
{
    protected function loadData(): void
    {
        parent::loadData();
        $this->data = array_map(function($line) {
            return str_split($line);
        }, $this->data);
    }

    protected function part1(): Result
    {
        $visible = 0;

        foreach ($this->data as $rowIndex => $row) {
            foreach ($row as $colIndex => $tree) {
                if ($this->isVisible($rowIndex, $colIndex, $tree)) {
                    $visible++;
                }
            }
        }
        return new Result(Result::PART1, $visible);
    }

    protected function part2(Result $part1): Result
    {
        $score = 0;

        foreach ($this->data as $rowIndex => $row) {
            foreach ($row as $colIndex => $tree) {
                $ourScore = $this->getScenicScore($rowIndex, $colIndex, $tree);
                $score = max($score, $ourScore);
            }
        }
        return new Result(Result::PART2, $score);
    }

    protected function isVisible(int $rowIndex, int $colIndex, int $height): bool
    {
        if ($rowIndex === 0 || $colIndex === 0) {
            return true;
        }
        if ($rowIndex === count($this->data) - 1 || $colIndex === count($this->data[0]) - 1) {
            return true;
        }

        $set = [
            $this->getUp($rowIndex, $colIndex),
            $this->getDown($rowIndex, $colIndex),
            $this->getLeft($rowIndex, $colIndex),
            $this->getRight($rowIndex, $colIndex),
        ];

        $hidden = 0;
        foreach ($set as $direction) {
            foreach ($direction as $tree) {
                if ($tree >= $height) {
                    $hidden++;
                    break;
                }
            }
        }

        return $hidden !== 4;
    }

    protected function getScenicScore(int $rowIndex, int $colIndex, int $height): int
    {
        $scores = [];
        $set = [
            $this->getUp($rowIndex, $colIndex),
            $this->getDown($rowIndex, $colIndex),
            $this->getLeft($rowIndex, $colIndex),
            $this->getRight($rowIndex, $colIndex),
        ];
        foreach ($set as $direction) {
            $score = 0;
            $direction = array_reverse($direction);
            foreach ($direction as $tree) {
                $score++;
                if ($tree >= $height) {
                    break;
                }
            }
            $scores[] = $score;
        }
        return array_product($scores);
    }

    protected function getUp(int $rowIndex, int $colIndex): array
    {
        $result = [];
        for($i = 0; $i < $rowIndex; $i++) {
            $result[] = $this->data[$i][$colIndex];
        }
        return $result;
    }

    protected function getDown(int $rowIndex, int $colIndex): array
    {
        $result = [];
        for($i = count($this->data) - 1; $i > $rowIndex; $i--) {
            $result[] = $this->data[$i][$colIndex];
        }
        return $result;
    }

    protected function getLeft(int $rowIndex, int $colIndex): array
    {
        return array_slice($this->data[$rowIndex], 0, $colIndex);
    }

    protected function getRight(int $rowIndex, int $colIndex): array
    {
        $slice = array_slice($this->data[$rowIndex], $colIndex + 1);
        return array_reverse($slice);
    }
}