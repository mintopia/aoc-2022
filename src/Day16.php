<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

// Heavily influenced by https://github.com/vinnymaker18/adventofcode/blob/main/2022/day16/program.py
class Day16 extends Day
{
    protected const TITLE = 'Proboscidea Volcanium';
    protected array $valves = [];
    protected array $graph = [];

    protected function loadData(): void
    {
        parent::loadData();
        $allValves = [];
        foreach ($this->data as $datum) {
            preg_match('/^Valve (?<name>.*) has flow rate=(?<rate>\d+).*valve(s)? (?<links>.*)$/', $datum, $matches);
            $name = $matches['name'];
            $rate = (int) $matches['rate'];
            $edges = explode(', ', $matches['links']);
            $valve = (object) [
                'name' => $name,
                'rate' => $rate,
                'edges' => $edges,
            ];
            $allValves[$name] = $valve;
            if ($rate > 0 || $name === 'AA') {
                $this->valves[$name] = $valve;
            }
        }

        foreach ($allValves as $i => $valve) {
            $this->graph[$i] = [];
            foreach ($allValves as $j => $valve2) {
                if ($i === $j) {
                    $this->graph[$i][$j] = 0;
                    continue;
                }
                $this->graph[$i][$j] = PHP_INT_MAX;
                if (in_array($j, $valve->edges)) {
                    $this->graph[$i][$j] = 1;
                }
            }
        }

        foreach ($allValves as $i => $v1) {
            foreach ($allValves as $j => $v2) {
                foreach ($allValves as $k => $v3) {
                    $this->graph[$j][$k] = min($this->graph[$j][$k], $this->graph[$j][$i] + $this->graph[$i][$k]);
                }
            }
        }
    }

    protected function part1(): Result
    {
        $options = $this->getBestFlows(30);
        $answer = end($options);
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        // 26 seconds after teaching an elephant, get all possible best solutions for each path
        $myOptions = $this->getBestFlows(26);

        $count = count($this->valves);

        // For each combination of visited valves, get the best flow rate
        $table = array_fill(0, 1 << $count, 0);
        foreach ($myOptions as $key => $flow) {
            [, $added, ] = explode(':', $key);
            $table[$added] = max($table[$added], $flow);
        }

        $answer = 0;

        // We now need to find two complimentary combinations that give the best flow rate.
        foreach (range(0, 1 << $count) as $mask) {
            $mask3 = ((1 << $count) - 1) ^ $mask;
            $answer = max($answer, $table[$mask3] ?? 0);

            $mask2 = $mask;
            while ($mask2 > 0) {
                $m3Value = $table[$mask3] ?? 0;
                $m2Value = $table[$mask2] ?? 0;
                $answer = max($answer, $m3Value + $m2Value);
                $mask2 = ($mask2 - 1) & $mask;
            }
        }
        return new Result(Result::PART2, $answer);
    }

    protected function getBestFlows(int $time): array
    {
        $lookup = [];
        $valves = [];
        $i = 0;
        foreach ($this->valves as $valve) {
            $lookup[$valve->name] = $i;
            $valves[$i] = $valve;
            $i++;
        }

        $queue = new \SplQueue();
        $best = [];

        $this->addToQueue($queue, $lookup['AA'], 0, 0, $time, $best);
        while (!$queue->isEmpty()) {
            [$valveIndex, $time, $added, $volume] = $queue->dequeue();
            if (($added & (1 << $valveIndex)) == 0 && $time >= 1) {
                // We haven't opened this valve, open it. Flow is our remaining time - 1 second to open it
                $flow = ($time - 1) * $valves[$valveIndex]->rate;
                $this->addToQueue($queue, $valveIndex, $volume + $flow, $added | (1 << $valveIndex), $time - 1, $best);
            }

            // Let's see if we do a move
            foreach ($valves as $valve) {
                $moveTime = $this->graph[$valves[$valveIndex]->name][$valve->name];
                if ($moveTime <= $time) {
                    $this->addToQueue($queue, $lookup[$valve->name], $volume, $added, $time - $moveTime, $best);
                }
            }
        }

        asort($best);
        return $best;
    }

    protected function addToQueue(\SplQueue $queue, int $valveIndex, int $volume, int $added, int $time, array &$best): void
    {
        if ($time < 0) {
            return;
        }
        $key = "{$valveIndex}:{$added}:{$time}";
        if (isset($best[$key]) && $best[$key] >= $volume) {
            // We have a better (or equal) volume for this time and state
            return;
        }
        $best[$key] = $volume;
        $queue->enqueue([$valveIndex, $time, $added, $volume]);
    }
}