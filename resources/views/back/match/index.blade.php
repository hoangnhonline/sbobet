@extends('back.template')

@section('main')
<?php 
function checkLeague($league_name){
	  $rs = true;
	  if($league_name == ''){
		$rs = false;
		return $rs;
	  }
	  $excludeArr = array('SPECIFIC 15 MINS', 'CORNERS', 'HOME TEAM/AWAY TEAM', 'OVER/UNDER', 'HALF', 'BOOKING', 'FREE KICK', 'GOAL KICK', 'Injury time awarded', 'OFFSIDE', 'OWN GOAL', 'PENALTY', 'RED CARD', 'GOAL', 'MINS', 'THROW IN', 'SUBSTITUTION', 'TOTAL GOALS', 'WHICH TEAM', 'EXTRA TIME', 'mins', 'ET', 'PEN');
	  
	  
	   foreach ($excludeArr as $value) {      
		  if(strpos($league_name, $value) > 0){          
			  $rs = false;
			  break;
		  }      
		 
	   }
	   return $rs;
	}
function checkMatch($team_name){
  $rs = true;
  if($team_name == ''){
    $rs = false;
    return $rs;
  }
  $excludeArr = array(
  '1st', '2nd', '3rd',
  '- Under', '- Over', '00',
  '- Over',
  'Last', 'No.of', 'Booking', 'Total',  'Half', 'Penalty', 'Card', 'Goal','Extra Time', 'ET', 'PEN'
  );
  for($i = 4; $i <= 100; $i++){
    $excludeArr[] = $i.'th';
  }
  
   foreach ($excludeArr as $value) {      
      if(strpos($team_name, $value) > 0){          
          $rs = false;
          break;
      }      
     
   }
   return $rs;
}

?>
 
  <div style="clear:both"></div>

  
  <div class="panel panel-default">    
    <div class="panel-body">        
      <form class="form-inline" role="form" action="" method="GET" id="formSearch">
    		<div class="form-group">
              <label for="league_id">League name</label>
    		  <select class="form-control selectpicker" data-live-search="true" name="league_id" id="league_id">
    			<option value="0">All</option>
    			@foreach($arrLeague as $k => $league)
    			<option value="{{ $league->league_id }}" {{ $league_id == $league->league_id ? "selected" : ""}}>{{ $league->league_name }}</option>
    			@endforeach
    		  </select>          
            </div> 
    		  <div class="form-group">
              <label for="league_id">Match status</label>
        		  <select class="form-control selectpicker" data-live-search="true" name="status" id="status">
        			
          			<option value="1" {{ $status == 1 ? "selected" : ""}}>Non-live</option>
          			<option value="2" {{ $status == 2 ? "selected" : ""}}>Live</option>
          			<option value="3" {{ $status == 3 ? "selected" : ""}}>End time</option>			
        		  </select>          
          </div> 
          <div class="form-group">
              <label for="league_id">Date</label><br>
              <input class="form-control datepicker" name="ondate" id="ondate" value="{{ $ondate }}" />                   
          </div> 
            <!--<div class="form-group">
              <label for="text">Team name</label><br/>
              <input type="text" class="form-control" id="team_name" name="team_name" value="{{ $team_name }}">
            </div>  -->
    		
        <button type="submit" class="btn btn-primary" style="margin-top:25px">Filter</button>
      </form>
    </div>
  </div>

   <div>   
   <h4> 
	<?php
	if($status == 1) echo "Non-live";
	if($status == 2) echo "Live";
	if($status == 3) echo "End time";
   ?> matches ( {{ $chuaDaArr->count() }} matches )</h4> 
  </div>
  <div class="">
    <div class="table-responsive ">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th width="1%" style="white-space:nowrap">Match ID</th>
			<th>
            Time
            </th>
            <th>
            Team
            </th>
			 <th>
            League
            </th>
            <th style="text-align:center !important;width:1%;white-space:nowrap">
              Bet Schedule
            </th>        
            <th width="150px">

            </th>
          </tr>
        </thead>
        <tbody>
          @if($chuaDaArr->count() > 0)         
          @foreach ($chuaDaArr as $match)
  
            
            <tr>              
              <td>{{ $match->ref_id }}</td>
			         <td>{{ strip_tags($match->time) }}</td>
              <td>{{ $match->team_name }} - {{ $match->team_name2 }}</td>
			       <td>{{ $match->league_name }}</td>
              <td class="text-center">
              @if($arrSchedule[$match->ref_id] > 0)
              <a href="{{ route('match.bet', [$match->ref_id]) }}" class='btn btn-primary'>
                <span class="badge btn btn-primary" >{{ isset($arrSchedule[$match->ref_id]) ? $arrSchedule[$match->ref_id] : "" }}</span>
                </a>
              @endif
              </td>
              <td>{!! link_to_route('match.bet', "Manage Bet", [$match->ref_id], ['class' => 'btn btn-warning btn-block']) !!}</td>
            </tr>
           
          @endforeach
			
          @else
          <tr><td colspan="6">No data found.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>

@stop

@section('scripts')
  <script>
	$(document).ready(function(){
			$('#league_id, #status, #ondate').change(function(){
				$('#formSearch').submit();
			});
      $('.datepicker').datepicker();
	});
  </script>

@stop
