<?php
namespace Mintopia\Aoc2022\Meta;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebInput extends Command
{
    protected static $defaultName = 'web:input';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client();
        $response = $client->get('https://adventofcode.com/2022/day/6', [
            'headers' => [
                'Cookie' => '',
            ],
            'verify' => false,
        ]);
        $content = (string) $response->getBody();

        print_r($content);
        return self::SUCCESS;
    }
}