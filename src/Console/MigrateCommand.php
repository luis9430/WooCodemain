<?php
namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Database\MigrationRunner;


class MigrateCommand extends Command
{
    protected static $defaultName = 'app:migrate';

    public function __construct()
    {
        parent::__construct(); // ✅ Necesario si usas $defaultName
    }

    protected function configure()
    {
        $this
        ->setName('app:migrate') // ✅ Esto evita usar $defaultName
        ->setDescription('Ejecuta las migraciones.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $runner = new MigrationRunner();
        $runner->run();

        $output->writeln('<info>All migrations have been run successfully.</info>');

        return Command::SUCCESS;
    }
}
