<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class AnimatedLink
{
    public string $url;
    public string $label;
    public string $color;
    public string $transition;

    public function mount(string $color): void
    {

        switch ($color) {
            case "primary":
                $this->transition =  " from-brandPrincipal-500 to-brandPrincipal-500";
                break;
            case "secondary":
                $this->transition = " from-brandSecondary-500 to-brandSecondary-500";
                break;
            case "third":
                $this->transition = " from-brandThird-500 to-brandThird-500";
                break;
            default:
                $this->transition = " from-gray-500 to-gray-500";
        }
    }
}
