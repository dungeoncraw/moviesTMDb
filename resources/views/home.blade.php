@extends('base')

@section('container')
	<h1>Movies List</h1>
	<div class="row">
		<div class="col-12 col-md-10 col-lg-8">
		    <form class="card card-sm">
		        <div class="card-body row no-gutters align-items-center">
		            <div class="col-auto">
		                <i class="fas fa-search h4 text-body"></i>
		            </div>
		            <!--end of col-->
		            <div class="col">
		                <input class="form-control form-control-lg form-control-borderless" id="movie-name" type="search" placeholder="Nome do filme para pesquisar">
		            </div>
		            <!--end of col-->
		            <div class="col-auto">
		                <button class="btn btn-lg btn-success" id="search-movie">Procurar</button>
						<button class="btn btn-lg btn-danger" id="clear-movie">Limpar</button>
		            </div>
		            <!--end of col-->
		        </div>
		    </form>
		</div>
		<!--end of col-->
	</div>
	<div class="container movie-container">
	</div>
@endsection

@section('scripts')
<script type="text/javascript">
	let pagination = 1;
	let totalPagination = 1;
	let totalResults = 0;
	$(document).ready(function(){
		
		$('#search-movie').on('click', function(){
			let term = $('#movie-name').val();
			if(term.length){
				searchMovie(term);
			}
		});

		$('#clear-movie').on('click', function(){
			pagination = 1;
			totalPagination = 1;
			totalResults = 0;
			$('.movie-container').empty();
			getMovies();
		});

		$(document).on('click', '.movie-detail', function(){
			let movieId = $(this).closest('.card').data('movie-id');
			console.log(movieId);
			$.ajax({
				url: 'http://localhost:8000/api/movies/detail?movie-id='+movieId,
				type: 'GET'
			}).done(function(data){
				console.log(data);
			});
		})

		getMovies();

		function searchMovie(term){
			console.log(term);
		};

		function getMovies(){
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
		}

		function loadMovieList(movies){
			pagination++;
			totalPagination = movies.total_pages;
			totalResults = movies.total_results;
			results = movies.results;
			for(movie in results){
				$('.movie-container').append(createMovieCard(results[movie]));
			}
		}

		function err(error){
			alert(error.message);
		}

		function getGenreName(genres){
			let genreNames = '';
			genres.forEach(function(item, index){
				genreNames += item + ' ';
			});
			return genreNames;
		}

		function getAlternativeImage(){
			return "this.onerror=null;this.src='https://via.placeholder.com/606x959.png?text=Filme+sem+imagem'";
		}

		function createMovieCard(movie){
			return '<div class="card" style="width: 38rem;" data-movie-id="'+movie.id+'">'+
			  '<img class="card-img-top" src="https://image.tmdb.org/t/p/w500/'+movie.poster_path+'" onerror="'+getAlternativeImage()+'" alt="'+movie.title+'">'+
			  '<div class="card-body">'+
			    '<h5 class="card-title">'+movie.title+'</h5>'+
			  '</div>'+
			  '<div class="card-footer text-muted">'+
			  '<p>Estreia: '+ movie.release_date + '</p>'+
			  '<p>Genero: ' + getGenreName(movie.genre_names) + '</p>'+
			  '<button class="btn btn-primary movie-detail">Detalhes</button>'+
			  '</div>'+
			'</div>'
		}
	});

</script>
@endsection