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

    public function mount($color){

        switch ($color){
            case "green" :
                $this->transition =  " from-brandGreen-500 to-brandGreen-500";
                break;
            case "blue" :
                $this->transition = " from-brandBlue-500 to-brandBlue-500";
                break;
            case "orange" :
                $this->transition = " from-brandOrange-500 to-brandOrange-500";
                break;
            default:
                $this->transition = " from-gray-500 to-gray-500";
        }


    }
}
