<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Infrastructure\Components;


use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/demo/filtered_table_component.html.twig')]
class FilteredTableComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public $values = [
        ['label' => 'Aspach-le-Bas'],
        ['label' => 'Aspach-Michelbach'],
        ['label' => 'Bitschwiller-lès-Thann'],
        ['label' => 'Bourbach-le-Bas'],
        ['label' => 'Bourbach-le-Haut'],
        ['label' => 'Cernay'],
        ['label' => 'Leimbach'],
        ['label' => 'Rammersmatt'],
        ['label' => 'Roderen'],
        ['label' => 'Schweighouse-Thann'],
        ['label' => 'Steinbach'],
        ['label' => 'Thann'],
        ['label' => 'Uffholtz'],
        ['label' => 'Vieux-Thann'],
        ['label' => 'Wattwiller'],
        ['label' => 'Willer-sur-Thur'],
        ['label' => 'Saint-Amarin'],
        ['label' => 'Fellering'],
        ['label' => 'Golbach-Altenbach'],
        ['label' => 'Kruth'],
        ['label' => 'Mitzach'],
        ['label' => 'Moosch'],
        ['label' => 'Storckensohn'],
        ['label' => 'Wildenstein'],
        ['label' => 'Geishouse'],
        ['label' => 'Husseren-Wesserling'],
        ['label' => 'Malmerspach'],
        ['label' => 'Mollau'],
        ['label' => 'Oderen'],
        ['label' => 'Ranspach'],
        ['label' => 'Urbès'],
    ];


    public function getCities(): array
    {
        if ($this->query === '') {
            return $this->values;
        }

        // example method that returns an array of Products
        return array_filter(
            $this->values,
            fn($value) => str_contains(strtolower($value['label']), strtolower($this->query))
        );
    }
}
