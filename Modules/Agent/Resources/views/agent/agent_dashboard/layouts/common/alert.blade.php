@if( session('message') || !empty($error) ||  $errors->any() || session('success') || session('error'))
	<div class="flash-container pb-1">
		@if(session('message'))
			<div class="alert {{ session('alert-class') }} text-center agent-margin-bottom" role="alert">
				{{ session('message') }}
				<a href="#" class="alert-close agent-float-right" data-dismiss="alert">&times;</a>
			</div>
		@endif

		@if(!empty($error))
			<div class="alert alert-danger text-center agent-margin-bottom" role="alert">
				{{ $error }}
				<a href="#" class="alert-close agent-float-right" data-dismiss="alert">&times;</a>
			</div>
		@endif

		@if($errors->any())
			<div class="alert alert-danger text-center agent-margin-bottom" role="alert">
				<a href="#" class="alert-close agent-float-right" data-dismiss="alert">&times;</a>
			@foreach ($errors->all() as $error)
					{{ $error }} <br/>
				@endforeach
			</div>
		@endif

		@if(session('success'))
			<div class="alert alert-success text-center agent-margin-bottom" role="alert">
				{{ session('success') }}
				<a href="#" class="alert-close agent-float-right" data-dismiss="alert">&times;</a>
			</div>
		@endif

		@if(session('error'))
			<div class="alert alert-danger text-center agent-margin-bottom" role="alert">
				{{ session('error') }}
				<a href="#" class="alert-close agent-float-right" data-dismiss="alert">&times;</a>
			</div>
		@endif
	</div>
@endif