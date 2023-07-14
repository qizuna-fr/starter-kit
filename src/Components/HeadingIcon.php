<?php


namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class HeadingIcon
{
    public string $iconHtml = '';
    public string $backgroundColor = '';
    public string $textColor = '';
    private string $color = '';
    public string $iconAlias = '';


    private const COLORS = [
        'success' => 'text-brandGreen-400',
        'warning' => 'text-brandOrange-400',
        'info' => 'text-brandBlue-400',
        'danger' => 'text-red-400',
    ];


    public function mount(string $type = "success", string $iconAlias = '')
    {
        $this->color = self::COLORS[$type];
    }




}
