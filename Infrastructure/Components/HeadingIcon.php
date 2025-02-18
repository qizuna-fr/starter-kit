<?php

namespace Infrastructure\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'heading-icon',)]
final class HeadingIcon
{
    public string $iconHtml = '';
    public string $backgroundColor = '';
    public string $textColor = '';
    public string $color = '';
    public string $iconAlias = '';


    private const COLORS = [
        'success' => 'text-green-400',
        'warning' => 'text-orange-400',
        'info' => 'text-blue-400',
        'danger' => 'text-red-400',
    ];


    public function mount(string $type = "success", string $iconAlias = ''): void
    {
        $this->color = self::COLORS[$type];
    }
}
