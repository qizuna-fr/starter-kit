<?php

declare(strict_types=1);

/** Qizuna 2025 - tous droits reservés  **/

namespace Domain\UserContext\Adapters\Primary\Command;


use DateInterval;
use DateTimeImmutable;
use Domain\AuthContext\Adapters\Secondary\Repositories\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

use function count;
use function sprintf;

#[AsCommand(
    name: 'app:delete-inactive-users',
    description: 'Delete definitely inactive user accounts created N days ago.'
)]
class DeleteUnactivatedUsers extends Command {

    public function __construct(
        private string $projectDir,
        private UserRepository $userRepository,
        private string $fromEmail,
        private string $fromName,
        private MailerInterface $mailer,
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('days', InputArgument::REQUIRED, 'Number of days since creation to target (N).');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $daysArg = (int)$input->getArgument('days');

        $today = new DateTimeImmutable();

        $dayStart = $today->sub(new DateInterval("P{$daysArg}D"))->setTime(0, 0, 0);
        $dayEnd = $today->sub(new DateInterval("P{$daysArg}D"))->setTime(23, 59, 59);

        $users = $this->userRepository->findInactiveUsersCreatedBetween($dayStart, $dayEnd);

        $emails = [];
        foreach ($users as $user) {
            $emails[] = $user->getEmail();
            $this->userRepository->remove($user, true);

            $email = (new TemplatedEmail())
                ->to($user->getEmail())
                ->from(new Address($this->fromEmail, $this->fromName))
                ->subject('Votre compte a été supprimé')
                ->htmlTemplate('emails/deleted_account.html.twig')
                ->context(
                    [
                        'username' => $user->getUsername(),
                    ],
                );
        }

        $io->success(
            sprintf(
                'Sent account deleted information %d user(s) created between at %s.',
                count($users),
                $dayStart->format('d/m/Y'),
            ),
        );

        return Command::SUCCESS;


    }


}
