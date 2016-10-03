<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Auto bet</title>
		<meta name="description" content="">	
		<meta name="viewport" content="width=device-width, initial-scale=1">

		{!! HTML::style('css/main_back.css') !!}

		<!--[if (lt IE 9) & (!IEMobile)]>
			{!! HTML::script('js/vendor/respond.min.js') !!}
		<![endif]-->
		<!--[if lt IE 9]>
			{{ HTML::style('https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js') }}
			{{ HTML::style('https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js') }}
		<![endif]-->

		{!! HTML::style('http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800') !!}
		{!! HTML::style('http://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic') !!}
        {!! HTML::style('http://code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css') !!}
        {!! HTML::style('css/select.min.css') !!}


        @yield('head')

	</head>

  <body>

	<!--[if lte IE 7]>
	    <p class="browsehappy">Vous utilisez un navigateur <strong>obsolète</strong>. S'il vous plaît <a href="http://browsehappy.com/">Mettez le à jour</a> pour améliorer votre navigation.</p>
	<![endif]-->

   <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                @if(session('statut') == 'admin')
                    {!! link_to_route('match.index', trans('back/admin.administration'), [], ['class' => 'navbar-brand']) !!}                
                @endif
            </div>
            <!-- Menu supérieur -->
            <ul class="nav navbar-right top-nav">                
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-user"></span> {{ auth()->user()->username }}<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{!! url('auth/logout') !!}"><span class="fa fa-fw fa-power-off"></span> {{ trans('back/admin.logout') }}</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Menu supérieur -->
            <ul class="nav navbar-right top-nav">                
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-ok"></span> {{ $provider == 1 ? "BONG88.COM" : "SBOBET.COM" }}<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        @if( $provider == 2)
                        <li>
                            <a href="{{ route('set-provider', ['provider' => 1]) }}"><span class="glyphicon glyphicon-log-in"></span> BONG88.COM</a>
                        </li>
                        @else
                        <li>
                            <a href="{{ route('set-provider', ['provider' => 2]) }}"><span class="glyphicon glyphicon-log-in"></span> SBOBET.COM</a>
                        </li>
                        @endif
                    </ul>
                </li>
            </ul>            
        </nav>

        <div id="page-wrapper">

            <div class="container-fluid">
                <div style="text-align:left;margin-bottom:5px;padding-left:0px" class="col-md-4">@if( $provider == 1)
                    <img class="img-thumbnail" src="{{ URL::asset('img/logo_bong88.png') }}">
                    @else
                    <img class="img-thumbnail" src="{{ URL::asset('img/logo.png') }}">
                    @endif
                </div>
                <div style="text-align:right;padding-right:0px" class="col-md-8">
                    @if( session('account_id') > 0 )
                        @if( $accountArr->count() > 0 )
                        
                      <ul id="list-account">
                        @foreach($accountArr as $account)
                        <li>
                          <a class="btn {{ session('account_id') == $account->id ? 'btn-danger' : 'btn-default' }}"  href="{{ route('set-account', ['account_id' => $account->id]) }}">{{ $account->username }}</a>
                        </li>                        
                   
                         @endforeach
                         <li>
                          <a class="btn btn-warning"  href="{{ route('manage-account') }}">Manage Account</a>
                        </li> 
                      </ul>
                          @endif
                    <ul id="list-chucnang">
                        <li>
                        <a class="btn btn-primary" href="{{ route('match.index') }}">Matches</a>
                      </li>
                      <li>
                        <a class="btn btn-primary" href="{{ route('league') }}">League Setup</a>
                      </li>
                      <li>
                        <a class="btn btn-info" href="{{ route('statement') }}">Statement</a>
                      </li>
                      <li>
                        <a class="btn btn-info" href="{{ route('schedule') }}">Report</a>
                      </li>
                      <!--<li>
                        <a class="btn btn-primary">Report</a>
                      </li>-->                    
                    </ul>
<div class="clearfix"></div>
                    <div style="text-align:right;margin-bottom:10px">
                        @if($detailAccount->run == 0)
                        <a class="btn btn-success" href="{{ route('update-run', ['run' => 1]) }}" onclick="return confirm('Are you sure you want to RUN this account ?');">RUN</a>
                        @else
                        <a class="btn btn-danger" href="{{ route('update-run', ['run' => 0]) }}" onclick="return confirm('Are you sure you want to STOP this account ?');">STOP</a>
                        @endif
                    </div>
                    <div class="clearfix"></div>
                    @endif
                  </div>
                @yield('main')

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /.page-wrapper -->

    </div>
    <!-- /.wrapper -->

    	{!! HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js') !!}
        {!! HTML::script('https://code.jquery.com/ui/1.10.0/jquery-ui.js') !!}
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
    	{!! HTML::script('js/plugins.js') !!}
    	{!! HTML::script('js/main.js') !!}
        {!! HTML::script('js/select.min.js') !!}
          
        <script type="text/javascript">
            $(document).ready(function(){
                $('.selectpicker').selectpicker();
            });
        </script>
        @yield('scripts')

  </body>
</html>
