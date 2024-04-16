<?php

namespace App\Command;

use App\Entity\PrintIO;
use App\Entity\PrintJob;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
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

    protected function configure(): void {
        $this->addArgument('file', InputArgument::REQUIRED, 'Argument description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        if (!file_exists($file)) {
            $io->error(sprintf('File does not exist: %s', $file));
            return Command::FAILURE;
        }
        if (!file_exists(self::PRINTER_DEVICE)) {
            $io->error(sprintf('device is not present: %s', self::PRINTER_DEVICE));
            return Command::FAILURE;
        }
        $section1 = $output->section();
        $section2 = $output->section();
        $section3 = $output->section();
        
        $section2->writeln('Input');
        $section3->writeln('Output');
        system("stty -F /dev/ttyUSB0 115200 raw -echo");

        $lines = system("wc -l < $file");

        $io->info("file is $file" . PHP_EOL
                . "gcode lines: " . $lines . PHP_EOL
                . "size is: " . stat($file)['size'] . PHP_EOL
        );
        $printJob = new PrintJob();
        $printJob->setDevice(self::PRINTER_DEVICE);
        $printJob->setFilename($file);

        $this->entityManager->persist($printJob);
        $this->entityManager->flush();

        $progressBar = new ProgressBar($section1, $lines);
        $progressBar->start();

        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (trim($line) === "") {
                    continue;
                }
                $section2->overwrite($line);
                $msg = "echo \"" . trim($line) . "\" >> " . self::PRINTER_DEVICE;
                $print = new PrintIO();
                $print->setInput($line);
                $print->setStatus(0);
                $print->setPrintjob($printJob);
                $print->setStart(new \DateTime());
                $this->entityManager->persist($print);
                $this->entityManager->flush();
                if (str_starts_with(trim($line), ";")) {
                    $print->setStatus(1);
                    $print->setEnd(new \DateTime());
                    $print->setOutput("");
                } else {
                    system($msg);
                    $this->processOutput($print, $section3);
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

    public function processOutput($print, $section3) {
        $stream = fopen(self::PRINTER_DEVICE, 'r');
        stream_set_timeout($stream, 2, 0);
        if ($stream) {
            $message = "";
            while (true) {
                // Odczytaj dane z urządzenia
                $line = fgets($stream); // Możesz zmienić rozmiar bufora, jeśli to konieczne
                $section3->overwrite($line);
                // Sprawdzenie, czy fread zwrócił dane
                if ($line === false) {
                    // Sprawdzenie, czy wystąpił timeout
                    $meta = stream_get_meta_data($stream);
                    if ($meta['timed_out']) {
                        throw new \Exception("timeout!");
                    }
                } else {
                    if (str_starts_with(trim($line), "ok")) {
                        if ($print) {
                            $print->setEnd(new \DateTime());
                            $print->setOutput($message);
                            $print->setStatus(1);
                            $this->entityManager->flush();
                        }
                        $message = "";
                        break;
                    }
                    //T:([0-9.]+) \/([0-9.]+) B:([0-9.]+) \/([0-9.]+) @:([0-9.]+) B@:([0-9.]+)( W:([0-9.?]+))?
                    else {
                        $message .= $line;
                    }
                }
            }
            fclose($stream);
        } else {
            echo "output " . self::PRINTER_OUTPUT . " is not readable!";
        }
    }
}
