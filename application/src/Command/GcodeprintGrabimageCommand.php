<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'gcodeprint:grabimage',
    description: 'Grabs an image from webcam',
)]
class GcodeprintGrabimageCommand extends Command
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $section1 = $output->section();
        $section2 = $output->section();

        $section1->writeln('Hello');
        $section2->writeln('World!');
        sleep(1);
        // Output displays "Hello\nWorld!\n"

        // overwrite() replaces all the existing section contents with the given content
        $section1->overwrite('Goodbye');
        sleep(1);
        // Output now displays "Goodbye\nWorld!\n"

        // clear() deletes all the section contents...
        $section2->clear();
        sleep(1);
        // Output now displays "Goodbye\n"

        // ...but you can also delete a given number of lines
        // (this example deletes the last two lines of the section)
        $section1->clear(2);
        sleep(1);
        // Output is now completely empty!

        // setting the max height of a section will make new lines replace the old ones
        $section1->setMaxHeight(2);
        $section1->writeln('Line1');
        $section1->writeln('Line2');
        $section1->writeln('Line3');
        return Command::SUCCESS;
    }
}
