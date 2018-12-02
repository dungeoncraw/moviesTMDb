@extends('base')

@section('container')
	<h1>Movies List</h1>
	<div class="container movie-container">
	</div>
@endsection

@section('scripts')
<script type="text/javascript">
	let pagination = 1;
	let totalPagination = 1;
	let totalResults = 0;
	$(document).ready(function(){
		console.log('foi');
		$.ajax({
			url: 'http://localhost:8000/api/movies/upcoming?page='+pagination,
			type: 'GET'
		}).done(function(data){
			if(data.status === 'ok'){
				loadMovieList(data.movies);
			} else {
				err(data)
			}
		});
	});

	function loadMovieList(movies){
		console.log(movies);
		pagination++;
		totalPagination = movies.total_pages;
		totalResults = movies.total_results;
		results = movies.results;
		for(movie in results){
			// console.log(results[movie]);
			console.log(createMovieCard(results[movie]));
			$('.movie-container').append(createMovieCard(results[movie]));
		}
	}

	function err(error){
		console.log(error);
	}

	function createMovieCard(movie){
		return '<div class="card" style="width: 38rem;">'+
		  '<img class="card-img-top" src="https://image.tmdb.org/t/p/w500/'+movie.poster_path+'" alt="'+movie.title+'">'+
		  '<div class="card-body">'+
		    '<h5 class="card-title">'+movie.title+'</h5>'+
		    '<p class="card-text">'+movie.overview+'</p>'+
		    '<a href="#" class="btn btn-primary">Detalhes</a>'+
		  '</div>'+
		'</div>'
	}
</script>
@endsection