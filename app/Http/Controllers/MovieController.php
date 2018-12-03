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
        $page = $this->getPagination($request);
        if($page <= 0){
            return response()->json(['status' => 'nok', 'message' => 'Paginação deve ser um número inteiro e maior que zero.']);
        }
        $urlMovies = $this->tmdbBaseUrl.'/movie/upcoming?api_key='.$this->tmdbApiKey.'&language=pt-BR&page='.$page;
        $response = $this->httpClient->get($urlMovies);
        $movies = json_decode($response->getBody()->getContents());

        if (count($movies->results)){
            $movies->results = $this->getGenre($movies->results);
        }

        return response()->json(['status' => 'ok', 'movies' => $movies]);
    }

    public function show(Request $request)
    {
        $movieId = $request->input('movie-id');
        if(!$movieId)
        {
            return response()->json(['status' => 'nok', 'message' => 'Identificador de filme inválido.']);
        }

        $movieDetailUrl = $this->tmdbBaseUrl.'/movie/'.$movieId.'?api_key='.$this->tmdbApiKey.'&language=pt-BR';
        $response = $this->httpClient->get($movieDetailUrl);
        $movieDetail = json_decode($response->getBody()->getContents());
        $movieDetail = $this->getGenre([$movieDetail]);
        $this->registerMovieView($request, $movieDetail);
        return response()->json(['status' => 'ok', 'movies' => $movieDetail]);
    }

    public function searchFilm(Request $request)
    {
        $movieName = $request->input('movie-name');
        if(strlen($movieName) < 3)
        {
            return response()->json(['status' => 'nok', 'message' => 'Informe ao menos 3 letras para pesquisar um filme.']);
        }

        $page = $this->getPagination($request);
        if($page <= 0){
            return response()->json(['status' => 'nok', 'message' => 'Paginação deve ser um número inteiro e maior que zero.']);
        }

        $urlMovies = $this->tmdbBaseUrl.'/search/movie?api_key='.$this->tmdbApiKey.'&language=pt-BR&query='.$movieName.'&page='.$page;
        $response = $this->httpClient->get($urlMovies);
        $movies = json_decode($response->getBody()->getContents());

        if (count($movies->results)){
            $movies->results = $this->getGenre($movies->results);
        }

        return response()->json(['status' => 'ok', 'movies' => $movies]);

    }

    private function getGenre($movies)
    {
        $urlGenreMovies = $this->tmdbBaseUrl.'/genre/movie/list?api_key='.$this->tmdbApiKey.'&language=pt-BR';
        $response = $this->httpClient->get($urlGenreMovies);
        $genreList = json_decode($response->getBody()->getContents());
        
        foreach($movies as $movie) 
        {
            $movie->genre_names = [];
            if(property_exists($movie, 'genre_ids')){
                $genreKey = 'genre_ids';
            } else {
                $genreKey = 'genres';
            }
            foreach($movie->$genreKey as $genre)
            {
                if($genreKey == 'genre_ids'){
                    $genreInfo = $genre;
                } else {
                    $genreInfo = $genre->id;
                }
                $genreName = array_search($genreInfo, array_column($genreList->genres, 'id'));
                array_push($movie->genre_names, $genreList->genres[$genreName]->name);
            }
        }
        return $movies;
    }

    private function getPagination($request)
    {
        //simple stratagy to get pagination from frontend
        //could implement pagination storage in backend
        $page = $request->input('page');
        return $page;
    }

    private function registerMovieView($request, $movieDetail)
    {
        //could store info about click and get metrics of visualization
        //to mvp just skip function
        
    }

    private function registerMovieSearch($request, $movieDetail)
    {
        //could store info about click and get metrics of visualization
        
    }    
    
}
