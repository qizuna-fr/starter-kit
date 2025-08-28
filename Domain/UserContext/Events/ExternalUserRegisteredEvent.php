<?php

declare(strict_types=1);

namespace Domain\UserContext\Events;

use Infrastructure\Entities\User;
use Symfony\Contracts\EventDispatcher\Event;

class ExternalUserRegisteredEvent extends Event
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
