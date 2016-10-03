@extends('back.template')

@section('main')
  <div class="col-md-12" style="padding:30px;text-align:left;height:600px;padding-left:0px">
  	 
  	 	<h3 style="padding-left:0px;text-align:left">Please choice account</h3>
      <ul id="list-account" style="padding-left:0px">
      	@if( $accountArr->count() > 0 )
        @foreach($accountArr as $account)
        <li>
          <a href="{{ route('set-account', ['account_id' => $account->id]) }}" class="btn {{ session('account_id') == $account->id ? 'btn-danger' : 'btn-default' }}">{{ $account->username }}</a>
        </li>            
        @endforeach
        @endif
      </ul>             
  </div>
@stop
