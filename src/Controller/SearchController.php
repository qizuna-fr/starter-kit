<?php

namespace App\Controller;

use Meilisearch\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function array_filter;
use function array_map;
use function count;
use function stripos;

final class SearchController extends AbstractController
{
    private $values = [
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

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    #[Route('/search', name:'app_search_cities')]
    public function searchCities(Request $request)
    {

        $search = $request->get('q');
        $data =  array_filter($this->values, fn($item) => stripos($item['label'], $search) !== false);

        if (count($data) === 0) {
            return new Response('', Response::HTTP_OK);
        }

        return $this->render('partials/search_palette_result.html.twig', [
            'data' => $data
        ]);
    }
}
