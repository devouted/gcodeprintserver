<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'gcodeprint:readprinter',
    description: 'reads data from printer',
)]
class GcodeprintReadprinterCommand extends Command
{
    const PRINTER_OUTPUT = "/dev/pts/2";

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $stream = fopen(self::PRINTER_OUTPUT, 'r');

        if ($stream) {
            $message = "";
            while (($line = fgets($stream)) !== false) {
                if(trim($line)==="ok"){
                    $io->success($line);
                    $io->info($message);

                    $message="";
                    //mark first unmarked line as done
                }
                else{
                    $message .= $line;
                    $io->warning($line);
                }
            }
            fclose($stream);
        } else {
            echo "output ".self::PRINTER_OUTPUT." is not readable!";
        }
        return Command::SUCCESS;
    }
}
