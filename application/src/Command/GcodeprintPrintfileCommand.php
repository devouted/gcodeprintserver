<?php

namespace App\Command;

use App\Entity\PrintIO;
use App\Repository\PrintIORepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
            name: 'gcodeprint:printfile',
            description: 'Print a file',
    )]
class GcodeprintPrintfileCommand extends Command {

    public function __construct(private EntityManagerInterface $entityManager) {
        parent::__construct();
    }
    
    const COMMAND_BUFOR = 50;
    const PRINTER_DEVICE = "/dev/ttyUSB0";
    const PRINTER_OUTPUT = "/dev/ttyUSB0";

    protected function configure(): void {
        $this
                ->addArgument('file', InputArgument::REQUIRED, 'Argument description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        if (!file_exists($file)) {
            $io->error(sprintf('File does not exist: %s', $file));
        }
        system("stty -F /dev/ttyUSB0 115200 raw -echo");
        $lines = system("wc -l < $file");
        $io->info("file is $file" . PHP_EOL
                . "gcode lines: " . $lines . PHP_EOL
                . "size is: " . stat($file)['size'] . PHP_EOL
        );
        $progressBar = new ProgressBar($output, $lines);
        $progressBar->start();
        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {    
                if(trim($line)===""){
                    continue;
                }
//                $this->canRun();
                $msg = "echo \"" . trim($line) . "\" > " . self::PRINTER_DEVICE;
                $print = new PrintIO();
                $print->setInput($line);
                $print->setStatus(0);
                $print->setStart(new \DateTime());
                if(str_starts_with(trim($line), ";")){
                    $print->setStatus(1);
                    $print->setEnd(new \DateTime());
                    $print->setOutput("");
                }
                else{
                    system($msg);
                    $this->processOutput($print, $io);
                }
                $this->entityManager->persist($print);
                $this->entityManager->flush();
                $progressBar->advance();
            }
            fclose($handle);
        }
        $progressBar->finish();
        $output->write(PHP_EOL);
        return Command::SUCCESS;
    }

    private function canRun(): void {
        $count = $this->entityManager
                ->getRepository(PrintIO::class)
                ->count(['status' => 0]);
        if($count === self::COMMAND_BUFOR){
            sleep(5);
            $this->canRun();
        }
    }

    public function processOutput($print, $io) {
        $stream = fopen(self::PRINTER_OUTPUT, 'r');

        if ($stream) {
            $message = "";
            while (($line = fgets($stream)) !== false) {
                if(trim($line)==="ok"){
                    if($print){
                        $print->setEnd(new \DateTime());
                        $print->setOutput($message);
                        $print->setStatus(1);
                        $this->entityManager->flush();
                    }
                    $message="";
                    break;
                }
                //T:([0-9.]+) \/([0-9.]+) B:([0-9.]+) \/([0-9.]+) @:([0-9.]+) B@:([0-9.]+)( W:([0-9.?]+))?
                else{
                    $message .= $line;
                    $io->warning($line);
                }
            }
            fclose($stream);
        } else {
            echo "output ".self::PRINTER_OUTPUT." is not readable!";
        }
    }
}
