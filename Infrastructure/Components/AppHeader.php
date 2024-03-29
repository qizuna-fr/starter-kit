<?php

namespace Infrastructure\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class AppHeader
{
    public string $imageUrl;
    public string $overlayColor;
}
