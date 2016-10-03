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
<div class="panel panel-default">    
    <div class="panel-body">        
      <form class="form-inline" role="form" action="" method="POST" id="formSearchAjax">
        <div class="form-group">
              <label for="league_id">League name</label>
          <select class="form-control selectpicker" data-live-search="true" name="league_id" id="league_id_ajax">
          <option value="0">All</option>
          @foreach($arrLeague as $k => $league)
          <option value="{{ $league->league_id }}" {{ $league_id == $league->league_id ? "selected" : ""}}>{{ $league->league_name }}</option>
          @endforeach
          </select>          
            </div> 
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="match_id" value="{{ $match_id }}">
            <input type="hidden" id="schedule_id" value="{{ $schedule_id }}">
          <div class="form-group">
              <label for="league_id">Match status</label>
              <select class="form-control selectpicker" data-live-search="true" name="status" id="status_ajax">              
                <option value="1" {{ $status == 1 ? "selected" : ""}}>Non-live</option>
                <option value="2" {{ $status == 2 ? "selected" : ""}}>Live</option>                    
              </select>          
          </div> 
          <div class="form-group">
              <label for="league_id">Date</label><br>
              <input class="form-control datepicker" name="ondate" id="ondate_ajax" value="{{ $ondate }}" />                   
          </div> 
            <!--<div class="form-group">
              <label for="text">Team name</label><br/>
              <input type="text" class="form-control" id="team_name" name="team_name" value="{{ $team_name }}">
            </div>  -->
        
        <button type="button" id="btnFilterAjax" class="btn btn-primary" style="margin-top:25px">Filter</button>
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
            <th width="1%"></th>
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
          </tr>
        </thead>
        <tbody>
          @if($chuaDaArr->count() > 0)
          
          @foreach ($chuaDaArr as $match)
            @if(checkMatch($match->team_name)==true && checkLeague($match->league_name) == true)
            
            <tr>              
              <td><input type="checkbox" name="id_copy[]" class="checkboxCopy" value="{{ $match->ref_id }}"></td>
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
            </tr>
            @endif
          @endforeach
          @else
          <tr><td colspan="6">No data found.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
