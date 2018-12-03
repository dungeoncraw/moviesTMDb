@extends('base')

@section('container')
	<h1>Lista de filmes</h1>
	<div class="row">
		<div class="col-12 col-md-10 col-lg-8">
		    <div class="card card-sm" style="border: none;">
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
		    </div>
		</div>
		<!--end of col-->
	</div>
	<div class="container movie-container">
	</div>
	<div class="container">
		<button class="btn btn-success btn-block d-none" id="load-more-movies">Mais filmes</button>
	</div>
	<div class="modal fade" id="movieModal" tabindex="-1" role="dialog" aria-labelledby="movieModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content" style="width: 42em;">
	      <div class="modal-header">
	        <h5 class="modal-title" id="movieModalLabel"></h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
	      </div>
	    </div>
	  </div>
	</div>
@endsection

@section('scripts')
<script type="text/javascript">
	let pagination = 1;
	let totalPagination = 0;
	let totalResults = 0;
	let hostname = window.location.protocol +'//'+ window.location.hostname + ':'+ window.location.port;
	$(document).ready(function(){
		getMovies();
		$('#search-movie').on('click', function(){
			let term = $('#movie-name').val();
			if(term.length){
				clearPagination();
				$('.movie-container').empty();
				searchMovie(term);
			}
		});

		$('#clear-movie').on('click', function(){
			clearPagination();
			$('#movie-name').val('');
			$('.movie-container').empty();
			getMovies();
		});

		$(document).on('click', '.movie-detail', function(){
			let movieId = $(this).closest('.card').data('movie-id');
			$.ajax({
				url: hostname + '/api/movies/detail?movie-id='+movieId,
				type: 'GET'
			}).done(function(data){
				let options = {
					'backdrop' : true,
					'keyboard': true
				};
				let movieDetail = createMovieCard(data.movies[0], true);
				$('#movieModalLabel').text(data.movies[0].title);
				$('.modal-body').empty();
				$('.modal-body').html(movieDetail);
				$('#movieModal').modal(options);
			});
		})

		$('#load-more-movies').on('click', function(){
			if($(this).data('type') === 'upcoming'){
				getMovies();
			} else if($(this).data('type') === 'search'){
				let term = $(this).data('term');
				searchMovie(term);
			}
		});

		function searchMovie(term){
			if (totalPagination == 1 || pagination === totalPagination){
				return alert('Não há mais itens para pesquisar.');
			}
			$.ajax({
				url: hostname + '/api/movies/search?movie-name='+term+'&page='+pagination,
				type: 'GET'
			}).done(function(data){
				if(data.status === 'ok'){
					$('#load-more-movies').data('type', 'search');
					$('#load-more-movies').data('term', term);
					$('#load-more-movies').removeClass('d-none');
					loadMovieList(data.movies);
				} else {
					err(data);
				}
			});
		};

		function getMovies(){
			if (totalPagination === 1 || pagination === totalPagination){
				return alert('Não há mais itens para pesquisar.');
			}
			$.ajax({
				url: hostname + '/api/movies/upcoming?page='+pagination,
				type: 'GET'
			}).done(function(data){
				if(data.status === 'ok'){
					$('#load-more-movies').data('type', 'upcoming');
					$('#load-more-movies').removeClass('d-none');
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

		function createMovieCard(movie, overview = false){
			let detailInfo = '';
			let buttons = '<button class="btn btn-primary movie-detail">Detalhes</button>';
			let image = '<img class="img-thumbnail" src="https://image.tmdb.org/t/p/w500/'+movie.poster_path+'" onerror="'+getAlternativeImage()+'" alt="'+movie.title+'">';
			if (overview) {
				detailInfo += '<p class"card-text">' + movie.overview;
				detailInfo += '</p>';
				buttons = '';
				image = '<img class="card-img-bottom" src="https://image.tmdb.org/t/p/w500/'+movie.poster_path+'" onerror="'+getAlternativeImage()+'" alt="'+movie.title+'">';
			}

			return '<div class="card m-3" style="width: 38rem;" data-movie-id="'+movie.id+'">'+
			  image +
			  '<div class="card-body">'+
			    '<h5 class="card-title">'+movie.title+'</h5>'+
			  '</div>'+ detailInfo +
			  '<div class="card-footer text-muted">'+
			  '<p>Estreia: '+ movie.release_date + '</p>'+
			  '<p>Genero: ' + getGenreName(movie.genre_names) + '</p>'+
			  	buttons+
			  '</div>'+
			'</div>'
		}

		function clearPagination(){
			pagination = 1;
			totalPagination = 0;
			totalResults = 0;
			$('#load-more-movies').data('type', 'upcoming');
			$('#load-more-movies').addClass('d-none');
		}
	});

</script>
@endsection