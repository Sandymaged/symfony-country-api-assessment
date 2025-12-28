<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\CountrySyncService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

 // run it with :  php bin/console app:country-sync
#[AsCommand(name: 'app:country-sync', description: 'Sync countries data from REST Countries API')]
class CountrySyncCommand extends Command
{
    public function __construct(private CountrySyncService $syncService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting countries sync...');

        $this->syncService->syncCountries();

        $output->writeln('Countries synced successfully!');

        return Command::SUCCESS;
    }
}