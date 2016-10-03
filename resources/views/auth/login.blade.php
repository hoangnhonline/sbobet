<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Auto bet system</title>
		<meta name="description" content="">	
		<meta name="viewport" content="width=device-width, initial-scale=1">

		@yield('head')

		{!! HTML::style('css/main_front.css') !!}

		<!--[if (lt IE 9) & (!IEMobile)]>
			{!! HTML::script('js/vendor/respond.min.js') !!}
		<![endif]-->
		<!--[if lt IE 9]>
			{!! HTML::style('https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js') !!}
			{!! HTML::style('https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js') !!}
		<![endif]-->

		{!! HTML::style('http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800') !!}
		{!! HTML::style('http://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic') !!}

	</head>

  <body>

	<!--[if lt IE 8]>
		<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->	

	<main role="main" class="container">
		<div class="row">
			<div class="box">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					@if(session()->has('error'))
						@include('partials/error', ['type' => 'danger', 'message' => session('error')])
					@endif	
					<hr>	
					<h2 class="intro-text text-center">LOGIN</h2>
					<hr>							
					
					{!! Form::open(['url' => 'auth/login', 'method' => 'post', 'role' => 'form']) !!}	
					
					<div class="row">

						{!! Form::control('text', 12, 'log', $errors, 'Email') !!}
						{!! Form::control('password', 12, 'password', $errors, 'Password') !!}
						<span style="text-align:center">{!! Form::submit('Submit', ['col-lg-12']) !!}</span>
						

					</div>
					
					{!! Form::close() !!}				

				</div>
				<div class="col-md-4"></div>
			</div>
		</div>
	</main>

	
	{!! HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js') !!}
	<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
	{!! HTML::script('js/plugins.js') !!}
	{!! HTML::script('js/main.js') !!}

	@yield('scripts')

  </body>
</html>
	

