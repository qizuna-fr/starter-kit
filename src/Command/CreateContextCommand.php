<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

use function array_map;
use function mkdir;

#[AsCommand(
    name: 'app:create-context',
    description: 'Add a short description for your command',
)]
class CreateContextCommand extends Command
{
    private const PATHS = [
        //'/BusinessLogic',
        '/BusinessLogic/AppServices',
        '/BusinessLogic/DomainEvents',
        '/BusinessLogic/Gateways',
        '/BusinessLogic/Models',
        '/BusinessLogic/Repositories',
        '/BusinessLogic/UseCases',
        '/BusinessLogic/EventDispatcher',
        '/BusinessLogic/EventListener',
        '/BusinessLogic/UseCases',
        '/Adapters/Primary/Controllers',
        '/Adapters/Secondary/Entities',
        '/Adapters/Secondary/Gateways',
        '/Adapters/Secondary/Repositories',
    ];

    public function __construct(private string $projectDir)
    {
        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->addArgument('contextName', InputArgument::OPTIONAL, 'the context name without context suffix');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $context = $input->getArgument('contextName');

        array_map(function (string $path) use ($context){
            $realpath = Path::join($this->projectDir , "Domain" , $context."Context", $path)."\n";

            $filesystem = new Filesystem();
            $filesystem->mkdir($realpath);

        }, self::PATHS);

        $io->success("Context {$context} created ! ");

        return Command::SUCCESS;
    }
}
