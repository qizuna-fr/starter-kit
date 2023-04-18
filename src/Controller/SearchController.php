<?php


namespace App\Controller;


use App\Factories\MeilisearchFactory;
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

    /**
     * @param array[] $values
     */
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

//    #[Route('/search' , name:'app_search')]
//    public function search(Request $request , Client $client){
//
//        $search = $request->get('q');
//        $data =  $client->index('PhoneCall')->search($search);
//
//        if(count($data) === 0){
//            return new Response('', Response::HTTP_OK);
//        }
//
//        return $this->render('partials/search_palette_result.html.twig' , [
//            'data' => array_map(function($item){
//                return [
//                    'label' => $item['caller'],
//                    'link' => $this->urlGenerator->generate('app_phonecall_detail' , ['id' => $item['id']])
//                ];
//            }, $data->getHits())
//        ]);
//    }

    #[Route('/search' , name:'app_search_cities')]
    public function searchCities(Request $request){

        $search = $request->get('q');
        $data =  array_filter($this->values, fn($item) => stripos($item['label'], $search) !== false );

        if(count($data) === 0){
            return new Response('', Response::HTTP_OK);
        }

        return $this->render('partials/search_palette_result.html.twig' , [
            'data' => $data
        ]);
    }

//    #[Route('/search/movies' , name:'app_search_movies')]
//    public function searchMovies(Request $request){
//
//        $client = new Client('http://localhost:7700' , 'qizuna');
//
//        $search = $request->get('q');
//        $data =  $client->index('movies')->search($search);
//
//        if(count($data) === 0){
//            return new Response('', Response::HTTP_OK);
//        }
//
//        return $this->render('partials/search_palette_result.html.twig' , [
//            'data' => array_map(function($item){
//                return [
//                    'label' => $item['title'],
//                    'link' => $item['poster'],
//                ];
//            }, $data->getHits())
//        ]);
//    }



//    #[Route('/search/movies/index' , name:'app_search_index')]
//    public function index(Request $request){
//
//        $client = new Client('http://localhost:7700' , 'qizuna');
//
//        $movies_json = file_get_contents(__DIR__.'/movies.json');
//        $movies = json_decode($movies_json);
//
//        $client->index('movies')->addDocuments($movies);
//
//    }



}
