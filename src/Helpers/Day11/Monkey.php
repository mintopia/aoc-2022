<?php
namespace Mintopia\Aoc2022\Helpers\Day11;

class Monkey
{
    protected const OPERATION_ADDITION = '+';
    protected const OPERATION_MULTIPLICATION = '*';

    public array $items = [];
    protected string $operator;
    protected string $operand;
    public int $test;
    public int $ifTrue;
    public int $ifFalse;

    public int $inspections = 0;

    public function __construct(array $input)
    {
        $this->items = explode(', ', substr($input[1], 18));
        array_walk($this->items, 'intval');
        preg_match('/(?<operator>\+|\*) (?<operand>.*)/', $input[2], $operation);
        $this->operator = $operation['operator'];
        $this->operand = $operation['operand'];
        $this->test = $this->getLastNumber($input[3]);
        $this->ifTrue =  $this->getLastNumber($input[4]);
        $this->ifFalse =  $this->getLastNumber($input[5]);
    }

    public function inspectAndThrow(int $worryFactor, int $maxValue): array
    {
        $true = [];
        $false = [];
        foreach ($this->items as $item) {
            $this->inspections++;
            $operand = $this->operand;
            if ($operand === 'old') {
                $operand = $item;
            }
            if ($this->operator === self::OPERATION_ADDITION) {
                $item += $operand;
            } elseif ($this->operator === self::OPERATION_MULTIPLICATION) {
                $item *= $operand;
            }

            $item = floor($item / $worryFactor);
            $item = $item % $maxValue;

            if ($item % $this->test === 0) {
                $true[] = $item;
            } else {
                $false[] = $item;
            }
        }
        $this->items = [];
        return [$true, $false];
    }

    public function addItems(array $items): void
    {
        $this->items = array_merge($this->items, $items);
    }

    protected function getLastNumber(string $input): int
    {
        $parts = explode(' ', $input);
        return (int) end($parts);
    }
}