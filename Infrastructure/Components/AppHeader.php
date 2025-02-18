<?php

namespace Infrastructure\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'app_header')]
class AppHeader
{
    public string $imageUrl;
    public string $overlayColor;
}
