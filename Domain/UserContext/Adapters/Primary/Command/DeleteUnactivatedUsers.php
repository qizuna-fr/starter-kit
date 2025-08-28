<?php

declare(strict_types=1);

/** Qizuna 2025 - tous droits reservés  **/

namespace Domain\UserContext\Adapters\Primary\Command;


use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'app:delete-inactive-users',
    description: 'Delete definitely inactive user accounts created N days ago.'
)]
class DeleteUnactivatedUsers extends Command {



}
