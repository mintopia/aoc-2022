<?php
namespace Mintopia\Aoc2022\Meta;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunLatest extends Command
{
    protected static $defaultName = 'latest';

    protected InputInterface $input;
    protected OutputInterface $output;

    protected function configure(): void
    {
        $this->setDescription("Run latest day of Advent of Code");
        $this->addOption('test', 't',  InputOption::VALUE_NONE, 'Use test data');
        $this->addOption('benchmark', 'b',  InputOption::VALUE_NONE, 'Benchmark');
        $this->addOption('iterations', 'i',  InputOption::VALUE_OPTIONAL, 'Iterations for benchmark', 100);
        $this->addOption('part1', 'p', InputOption::VALUE_NONE, 'Only execute part 1');
        $this->addOption('noperformance', 'sphp aoc ', InputOption::VALUE_NONE, 'No performance output');
        $this->addOption('visualise', null, InputOption::VALUE_NONE, 'Enable visualisation');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        for ($i = 25; $i > 0; $i--) {
            if ($this->getApplication()->has("day{$i}")) {
                try {
                        $command = $this->getApplication()->find("day{$i}");
                        return $command->run($input, $output);
                } catch (\Exception $e) {
                    return self::FAILURE;
                }
            }
        }

        return self::FAILURE;
    }
}