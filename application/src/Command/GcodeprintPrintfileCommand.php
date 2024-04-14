<?php

namespace App\Command;

use App\Entity\PrintIO;
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
class GcodeprintPrintfileCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    const PRINTER_DEVICE = "/dev/pts/3";

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Argument description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        if (!file_exists($file)) {
            $io->error(sprintf('File does not exist: %s', $file));
        }
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
                while ($this->canRun() === true) {
                    $msg = "echo \"" . trim($line) . "\" > " . self::PRINTER_DEVICE;
                    $print = new PrintIO();
                    $print->setInput($msg);
                    $print->setStatus(0);
                    $print->setStart(new \DateTime());
                    $this->entityManager->persist($print);
                    $this->entityManager->flush();
                    system($msg);
                    $progressBar->advance(1);
                }
            }
            fclose($handle);
        }
        $progressBar->finish();
        $output->write(PHP_EOL);
        return Command::SUCCESS;
    }

    private function canRun(): bool
    {
        $count = $this->entityManager
            ->getRepository(PrintIO::class)
            ->count(['status' => 0]);
        var_dump($count);
        sleep(5);
        return $count < 2;
    }
}
