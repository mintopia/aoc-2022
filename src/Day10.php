<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\ASCIIText;
use Mintopia\Aoc2022\Helpers\Result;

class Day10 extends Day
{
    protected const TARGET_CYCLES = [
        20, 60, 100, 140, 180, 220,
    ];

    protected function part1(): Result
    {
        $cycle = 1;
        $xRegister = 1;
        $strength = [];
        $image = [];
        foreach ($this->data as $line) {
            $parts = explode(' ', $line);
            [$strength, $image] = $this->updateCycle($strength, $image, $cycle, $xRegister);
            switch ($parts[0]) {
                case 'noop':
                    // Advance to next cycle
                    $cycle++;
                    break;
                case 'addx':
                    //Adjust register X
                    $cycle++;
                    [$strength, $image] = $this->updateCycle($strength, $image, $cycle, $xRegister);
                    $cycle++;
                    $xRegister += (int) $parts[1];
                    break;
            }
        }

        $answer = array_sum($strength);
        return new Result(Result::PART1, $answer, $image);
    }

    protected function part2(Result $part1): Result
    {
        $ascii = new ASCIIText($part1->carry);
        $ascii->render($this->io);
        $answer = $ascii->ocr();
        return new Result(Result::PART2, $answer);
    }

    protected function updateCycle(array $strength, array $image, int $cycle, int $xRegister): array
    {
        if (in_array($cycle, self::TARGET_CYCLES)) {
            $strength[$cycle] = $cycle * $xRegister;
        }
        $image = $this->updateImage($image, $cycle, $xRegister);
        return [$strength, $image];
    }

    protected function updateImage(array $image, int $cycle, int $xRegister): array
    {
        $position = ($cycle - 1) % 40;
        $line = floor(($cycle - 1) / 40);
        if ($position === 0) {
            $image[] = '';
        }
        if (in_array($position, range($xRegister - 1, $xRegister + 1))) {
            $image[$line] .= 1;
        } else {
            $image[$line] .= 0;
        }
        return $image;
    }
}