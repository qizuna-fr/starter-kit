<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Notification
{
    public string $color = '';
    public string $title = '';
    public string $message = '';

    private const COLORS = [
        'success' => 'text-brandGreen-400',
        'warning' => 'text-brandOrange-400',
        'info' => 'text-brandBlue-400',
        'danger' => 'text-red-400',
        'error' => 'text-red-400',
    ];

    private const TITLES = [
        'success' => 'Parfait ! ',
        'warning' => 'Attention ! ',
        'info' => 'Info ',
        'danger' => 'Erreur',
        'error' => 'Erreur',
    ];

    public function mount(string $type = "success"): void
    {
        $this->color = self::COLORS[$type];
        $this->title = self::TITLES[$type];
    }
}
