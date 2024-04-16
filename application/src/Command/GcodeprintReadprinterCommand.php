<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PrintIORepository;
use App\Entity\PrintIO;

#[AsCommand(
    name: 'gcodeprint:readprinter',
    description: 'reads data from printer',
)]
class GcodeprintReadprinterCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager, private PrintIORepository $printIORepository) {
        parent::__construct();
    }
    const PRINTER_OUTPUT = "/dev/ttyUSB0";

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        system("stty -F /dev/ttyUSB0 115200 raw -echo");
        
        $stream = fopen(self::PRINTER_OUTPUT, 'r');

        if ($stream) {
            $message = "";
            while (($line = fgets($stream)) !== false) {
                if(trim($line)==="ok"){
                    $io->success($line);
                    $io->info($message);

                    $print = $this->printIORepository->getLastEntry();
                    if($print){
                        $print->setEnd(new \DateTime());
                        $print->setOutput($message);
                        $print->setStatus(1);
                        $this->entityManager->flush();
                    }
                    $message="";
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
