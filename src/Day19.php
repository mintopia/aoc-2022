<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Day19\Blueprint;
use Mintopia\Aoc2022\Helpers\Result;

class Day19 extends Day
{
    protected array $blueprints = [];

    protected const MATERIALS = [
        'geode',
        'obsidian',
        'clay',
        'ore',
    ];

    protected function loadData(): void
    {
        parent::loadData();
        $this->blueprints = array_map(function(string $line): Blueprint {
            return new Blueprint($line);
        }, $this->data);
    }

    protected function part1(): Result
    {
        $scores = [];
        foreach ($this->blueprints as $blueprint) {
            $geodes = $this->getMaxGeodes($blueprint, 24);
            $scores[] = $blueprint->id * $geodes;
        }
        $answer = array_sum($scores);
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $scores = [];
        $slice = array_slice($this->blueprints, 0, 3);
        foreach ($slice as $blueprint) {
            $scores[] = $this->getMaxGeodes($blueprint, 32);
        }
        $answer = array_product($scores);
        return new Result(Result::PART2, $answer);
    }

    protected function getMaxGeodes(Blueprint $blueprint, int $minutes): int
    {
        $history = [];
        $best = 0;

        $state = [
            'robots' => [
                'ore' => 1,
                'clay' => 0,
                'obsidian' => 0,
                'geode' => 0,
            ],
            'materials' => [
                'ore' => 0,
                'clay' => 0,
                'obsidian' => 0,
                'geode' => 0,
            ],
        ];

        return $this->dfs($blueprint, $minutes, $state, $history, $best);
    }

    protected function dfs(Blueprint $blueprint, int $time, array $state, array &$history, int &$best): int
    {
        // Check we aren't out of time
        if ($time == 0) {
            return $state['materials']['geode'];
        }

        // Maintain a history of state + scores, return if we find it in the state
        $key = json_encode($state);
        if (isset($history[$key])) {
            return $history[$key];
        }

        // If we couldn't possibly earn more than our best in the remaining time, finish now
        $geodes = $state['materials']['geode'] + ($state['robots']['geode'] * $time);
        $possible = $geodes + round(($time * ($time + 1)) / 2);
        if ($possible < $best) {
            return $geodes;
        }

        // Our options are to build one of 4 robot types
        foreach (self::MATERIALS as $robotType) {
            // We don't want to build more robots than are needed
            if ($state['materials'][$robotType] >= $blueprint->max[$robotType]) {
                // We don't need more of this robot type
                continue;
            }

            // If we need to wait to gather more resources to build the robot, work out how long to wait
            $timeToWait = 0;
            foreach ($blueprint->costs[$robotType] as $mat => $cost) {
                // If no cost, no need to wait
                if ($cost === 0) {
                    continue;
                }

                // If we don't have a robot that can collect this resource, we can't build the robot
                if ($state['robots'][$mat] == 0) {
                    // Can't build this robot
                    continue 2;
                }

                // Wait until we have enough resources
                $timeToWait = max($timeToWait, ceil(($cost - $state['materials'][$mat]) / $state['robots'][$mat]));
            }

            // See if we have the time to build it
            $remaining = $time - $timeToWait - 1;
            if ($remaining <= 0) {
                continue;
            }

            $newState = $state;

            // Harvest our new resources for however long we've waited + our current iteration
            foreach (self::MATERIALS as $mat) {
                $newState['materials'][$mat] += ($newState['robots'][$mat] * ($timeToWait + 1));
            }

            // Build the new robot
            $newState = $blueprint->build($newState, $robotType);

            // If we've exceeded the max materials we could ever make, reduce to the max to improve caching
            foreach (self::MATERIALS as $mat) {
                $newState['materials'][$mat] = min($newState['materials'][$mat], $blueprint->max[$mat] * $remaining);
            }

            // Recurse into the next choice and store the best result
            $childGeodes = $this->dfs($blueprint, $remaining, $newState, $history, $best);
            $geodes = max($geodes, $childGeodes);
            $best = max($best, $childGeodes);
        }

        $history[$key] = $geodes;
        return $geodes;
    }
}