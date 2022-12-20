<?php
namespace Mintopia\Aoc2022\Helpers\Day19;

class Blueprint {
    public int $id;
    public array $costs = [];

    public array $max = [
        'ore' => 0,
        'clay' => 0,
        'obsidian' => 0,
        'geode' => INF,
    ];

    public int $maxRobots = 0;

    public function __construct(string $input)
    {
        preg_match('/Blueprint (?<id>\d+).*ore robot costs (?<ore>\d+).*clay robot costs (?<clay>\d+).*obsidian robot costs (?<obore>\d+) ore and (?<obclay>\d+).*geode robot costs (?<geodeore>\d+) ore and (?<geodeobsidian>\d+)/', $input, $matches);
        $this->id = (int) $matches['id'];

        $this->costs['ore'] = [
            'ore' => (int) $matches['ore'],
            'clay' => 0,
            'obsidian' => 0,
        ];
        $this->costs['clay'] = [
            'ore' => (int) $matches['clay'],
            'clay' => 0,
            'obsidian' => 0,
        ];
        $this->costs['obsidian'] = [
            'ore' => (int) $matches['obore'],
            'clay' => (int) $matches['obclay'],
            'obsidian' => 0,
        ];
        $this->costs['geode'] = [
            'ore' => (int) $matches['geodeore'],
            'clay' => 0,
            'obsidian' => (int) $matches['geodeobsidian'],
        ];

        foreach ($this->costs as $cost) {
            foreach ($cost as $mat => $amount) {
                $this->max[$mat] = max($this->max[$mat], $amount);
            }
        }
    }

    public function build(array $state, string $robotType): array
    {
        $state['robots'][$robotType]++;
        foreach ($this->costs[$robotType] as $mat => $cost) {
            $state['materials'][$mat] -= $cost;
        }
        return $state;
    }
}