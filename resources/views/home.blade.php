@extends('base')

@section('container')
	<h1>agora vai</h1>
@endsection

@section('scripts')
<script type="text/javascript">
	$(document).ready(function(){
		console.log('foi');
		$.ajax({
			url: 'http://localhost:8000/movies/upcoming?page=2',
			type: 'GET'
		}).done(function(data){
			console.log(data);
		});
	})
</script>
@endsection