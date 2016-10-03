@extends('back.template')

@section('main')
<div class="col-md-12">
@if(Session::has('flash_message'))
      <div class="alert alert-success">
          {{ Session::get('flash_message') }}
      </div>
  @endif
</div>
<div class="col-md-6" style="padding-left:0px">

 <div class="">   
 <h4>Leagues ( {{ count($arrLeague) }})</h4> 
</div>
<div style="clear:both"></div>
<div class="panel panel-default">    
    <div class="panel-body">        
      <form class="form-inline" role="form" action="" method="GET" id="formSearch">         
          <div class="form-group">
              <label for="league_id">League name</label><br>
              <input class="form-control" name="name" id="name" value="{{ $name }}" />                   
          </div>          
        
        <button type="submit" class="btn btn-primary" style="margin-top:25px">Search</button>
      </form>
    </div>
  </div>
<div class="">
  <div class="table-responsive ">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th width="1%" style="white-space:nowrap">ID</th>
          <th>Name</th>      
          <th width='1%'></th>    
        </tr>
      </thead>
      <tbody>   
        
        @foreach($arrLeague as $k => $league)
          
          <tr>              
            <td>{{ $league->league_id }}</td>
            <td>{{ strip_tags($league->league_name) }}</td>
            <td>
              @if(!in_array( $league->league_id, $selectedId ))
              <a href="{{ route('add-league', ['league_id' => $league->league_id])}}" class="btn btn-primary">Add</a></td>
              @else
              <label class="label label-danger">Added</label>
              @endif
          </tr>               
        @endforeach
       
      </tbody>
    </table>
  </div>
</div>
</div>
<div class="col-md-6" style="padding-right:0px">
   <div>   
   <h4>Added leagues</h4> 
  </div>
  <div>
    <div class="table-responsive ">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th width="1%" style="white-space:nowrap">ID</th>
            <th>Name</th>      
            <th width='1%'></th>    
          </tr>
        </thead>
        <tbody>   
          @if( count($arrLeagueSelected) > 0 )
          @foreach($arrLeagueSelected as $k => $league)
            
            <tr>              
              <td>{{ $league['league_id'] }}</td>
              <td>{{ strip_tags($league['league_name']) }}</td>
              <td><a href="{{ route('remove-league', ['league_id' => $league['league_id']])}}" onclick="return confirm('All matches and schedule bet of this league will be deleted. Are you sure you want to remove this league?')" class="btn btn-danger">Remove</a></td>
            </tr>
            
          @endforeach
         @else
         <tr><td colspan="3">No data found.</td></tr>
         @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

@stop

@section('scripts')
<script>
$(document).ready(function(){
		$('#league_id, #status').change(function(){
			$('#formSearch').submit();
		});
    $('.datepicker').datepicker();
});
</script>

@stop
