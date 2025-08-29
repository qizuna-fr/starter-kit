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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function count;

#[AsCommand(
    name: 'app:resend-activation-invite',
    description: 'Resend activation email to users created N days ago who are still inactive.'
)]
class ResendActivationInvite extends Command
{

    public function __construct(
        private string $projectDir,
        private MailerInterface $mailer,
        private UserRepository $userRepository,
        private UrlGeneratorInterface $urlGenerator,
        private string $fromEmail,
        private string $fromName,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('days', InputArgument::REQUIRED, 'Number of days since creation to target (N).');
        $this->addArgument(
            'deleteInDays',
            InputArgument::REQUIRED,
            'Number of days remaining before deletion if not activated.',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $daysArg = (int)$input->getArgument('days');
        $deleteInDaysArg = (int)$input->getArgument('deleteInDays');

        $today = new DateTimeImmutable();

        $dayStart = $today->sub(new DateInterval("P{$daysArg}D"))->setTime(0, 0, 0);
        $dayEnd = $today->sub(new DateInterval("P{$daysArg}D"))->setTime(23, 59, 59);

        $users = $this->userRepository->findInactiveUsersCreatedBetween($dayStart, $dayEnd);

        foreach ($users as $user) {
            $url = $this->urlGenerator
                ->generate(
                    'app_email_validate',
                    ['token' => $user->getActivationToken()],
                    UrlGeneratorInterface::ABSOLUTE_URL,
                );

            $email = (new TemplatedEmail())
                ->to($user->getEmail())
                ->from(new Address($this->fromEmail, $this->fromName))
                ->subject('Votre compte a été supprimé')
                ->htmlTemplate('emails/resend_activation_link.html.twig')
                ->context(
                    [
                        'url' => $url,
                        'days_to_registration' => $daysArg,
                        'user_email' => $user->getEmail(),
                        'username' => $user->getUsername(),
                        'deleted_in_days' => $deleteInDaysArg,
                    ],
                );

            $this->mailer->send($email);
        }

        $io->success(
            sprintf(
                'Resent activation invite to %d user(s) created between at %s.',
                count($users),
                $dayStart->format('d/m/Y'),
            ),
        );

        return Command::SUCCESS;
    }

}
