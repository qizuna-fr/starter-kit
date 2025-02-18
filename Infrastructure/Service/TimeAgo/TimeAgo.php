<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Infrastructure\Service\TimeAgo;


use DateInterval;
use DateTime;
use DateTimeInterface;

use function dump;
use function sprintf;

class TimeAgo
{

    const PAST_INDEX = 0;
    const FUTURE_INDEX = 1;

    private array $translations = [
        'fr' => [
            //'now' => 'à l\'instant',
            'second' => ['il y a %d seconde', 'dans une seconde'],
            'seconds' => ['il y a %d secondes', 'dans %d secondes'],
            'minute' => ['il y a une minute', 'dans une minute'],
            'minutes' => ['il y a %d minutes', 'dans %d minutes'],
            'hour' => ['il y a une heure', 'dans une heure'],
            'hours' => ['il y a %d heures', 'dans %d heures'],
            'day' => ['il y a un jour', 'demain'],
            'days' => ['il y a %d jours', 'dans %d jours'],
            'month' => ['il y a un mois', 'dans un mois'],
            'months' => ['il y a %d mois', 'dans %d mois'],
            'year' => ['il y a un an', 'dans un an'],
            'years' => ['il y a %d ans', 'dans %d ans'],
            'never' => ['jamais', 'jamais']
        ]
    ];
    private ?DateTimeInterface $now;

    public function __construct(DateTimeInterface $now = null)
    {
        $this->now = ($now == null) ? new \DateTime() : $now;
    }

    public function inWords(DateTimeInterface $date): string
    {
        $diff = $this->now->diff($date);
        return $this->getString($diff);

    }

    private function isInPast(DateInterval $dateDiffInvert){
        return $dateDiffInvert->invert === 1;
    }

    private function getString(DateInterval $dateInterval){

        $properties = [
            'year' => $dateInterval->y,
            'month' => $dateInterval->m,
            'day' => $dateInterval->d,
            'hour' => $dateInterval->h,
            'minute' => $dateInterval->i,
            'second' => $dateInterval->s
        ];

        foreach ($properties as $index => $value) {
            if ($value !== 0) {

                if($value > 1){
                    $index .='s';
                }

                $isPastIndex = $this->isInPast($dateInterval) ? self::PAST_INDEX : self::FUTURE_INDEX;
                $translation = $this->translations['fr'][$index][$isPastIndex];
                return sprintf($translation, $value);

            }
        }

        return null; // Retourne null si toutes les valeurs sont à 0

    }




}
