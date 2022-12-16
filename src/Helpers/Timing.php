<?php
namespace Mintopia\Aoc2022\Helpers;

use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Style\SymfonyStyle;

class Timing
{
    public int $dataLoading;
    public int $part1 = 0;
    public int $part2 = 0;

    public function getAverages(array $timings): void
    {
        $total = count($timings);
        if ($total == 0) {
            $this->dataLoading = 0;
            $this->part1 = 0;
            $this->part2 = 0;
            return;
        }

        $properties = [
            'dataLoading',
            'part1',
            'part2',
        ];

        foreach ($properties as $propName) {
            $this->{$propName} = round(array_reduce($timings, function(int $carry, Timing $timing) use ($propName) {
                    return $carry + $timing->{$propName};
                }, 0) / $total, 0);
        }
    }

    public function render(SymfonyStyle $io): void
    {
        $total = $this->dataLoading + $this->part1 + $this->part2;
        $table = [
            ['Data Loading', $this->ms($this->dataLoading)],
            ['Part 1', $this->ms($this->part1)],
            ['Part 2', $this->ms($this->part2)],
            new TableSeparator(),
            ['Total', $this->ms($total)],
            new TableSeparator(),
            ['Memory', $this->getMemoryUsage()],
        ];
        $io->title('Performance');
        $io->table(['Section', 'Time (ms)'], $table);
    }

    protected function getMemoryUsage(): string
    {
        $bytes = memory_get_peak_usage();
        $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.2f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    protected function ms(int $nanoSeconds): float
    {
        return round($nanoSeconds / 1000000, 3);
    }
}