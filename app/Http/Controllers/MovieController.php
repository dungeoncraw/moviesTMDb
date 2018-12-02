<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class MovieController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tmdbBaseUrl = 'https://api.themoviedb.org/3';
        $this->httpClient = new Client();
        $this->tmdbApiKey = env('TMDB_API_KEY');
    }

    public function index(Request $request)
    {
        $page = $request->input('page');
        
        if($page <= 0){
            return response()->json(['status' => 'nok', 'message' => 'Paginação deve ser um número inteiro e maior que zero.']);
        }

        $urlMovies = $this->tmdbBaseUrl.'/movie/upcoming?api_key='.$this->tmdbApiKey.'&language=pt-BR&page='.$page;
        $response = $this->httpClient->get($urlMovies);
        $movies = json_decode($response->getBody()->getContents());
        
        return response()->json(['status' => 'ok', 'movies' => $movies]);
    }

    public function show(Request $request)
    {

    }
    
}
