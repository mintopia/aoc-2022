<?php
namespace Mintopia\Aoc2022\Meta;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunAll extends Command
{
    protected static $defaultName = 'all';

    protected InputInterface $input;
    protected OutputInterface $output;

    protected function configure(): void
    {
        $this->setDescription("Run all days of Advent of Code");
        $this->addOption('test', 't',  InputOption::VALUE_NONE, 'Use test data');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        for ($i = 1; $i <= 25; $i++) {
            try {
                $command = $this->getApplication()->find("day{$i}");
                $command->run($input, $output);
                $output->writeln('');
            } catch (\Exception $e) {
                continue;
            }
        }

        return Command::SUCCESS;
    }
}