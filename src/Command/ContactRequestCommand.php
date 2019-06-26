<?php

namespace App\Command;

use App\Errors\ValidationError;
use App\Service\ContactRequestFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ContactRequestCommand extends Command
{
    protected static $defaultName = 'app:import:contact-requests';

    /**
     * @var ContactRequestFacade
     */
    private $contactRequestFacade;

    /**
     * @param ContactRequestFacade $contactRequestFacade
     */
    public function __construct(ContactRequestFacade $contactRequestFacade)
    {
        $this->contactRequestFacade = $contactRequestFacade;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Import contact requests');
        $this->addArgument('file', InputArgument::REQUIRED, 'CSV file path');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws ValidationError
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $progressBar = new ProgressBar($output);

        $hasStarted = false;
        $progressCallback = function (int $totalBytesCount, int $readBytesCount) use (&$hasStarted, $progressBar) {
            if (!$hasStarted) {
                $progressBar->start($totalBytesCount);
                $hasStarted = true;
            }

            $progressBar->advance($readBytesCount);
        };

        $this->contactRequestFacade->handleCsvImport(
            $input->getArgument('file'),
            $progressCallback
        );

        $progressBar->finish();
        $output->writeln("");
    }
}