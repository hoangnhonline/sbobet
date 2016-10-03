@extends('back.template')

@section('main')
<div class="clearfix"></div>

<div class="panel panel-default">    
    <div class="panel-body">        
      <form class="form-inline" role="form" action="" method="GET" id="formSearch">    		
          <div class="form-group">
              <label for="league_id">Date</label><br>
              <input class="form-control datepicker" name="ondate" id="ondate" value="{{ $ondate }}" />                   
          </div> 
        <button type="submit" class="btn btn-primary" style="margin-top:25px">Submit</button>
      </form>
    </div>
  </div>
 <div class="clearfix"></div>
  <h3 style="padding-left:0px;text-align:left">Statement : {{ $ondate }} ({{ $dataArr->count() }})</h3>
   <div class="col-md-6" style="padding:0px !important;"> 
      <table class="table table-bordered" id="current">
        <tr>
          <th width="1%">#</th>
          <th>Details</th>      
          <th>Selection</th>      
          <th>Odds</th>      
          <th>Stake</th>           
          <th>Status</th>           
        </tr>    
        @if(!empty($dataArr))
        <?php $i = 0; ?>
          @foreach($dataArr as $data)
          <?php $i++; ?>
          <tr>        
            <td>{{ $i }}</td>
            <td style="text-align:center"><span style='color:blue'>{{ $data->bet_id }}</span><br />{{ $data->bet_date }} <br >{{ $data->bet_time }}</td>

            <td style="text-align:right"><span style="color:red">{{ $data->choice_team }} {{ $data->bet_live_score  }}</span><br>
            {{ $data->bet_option }}<br />
            {{ $data->home_team }} -<span style="color:blue">vs</span>- {{ $data->away_team }}<br />
            {{ $data->league_name }} @ {{ $data->event_date }}

            </td>

            <td style="text-align:center"><span style="color:blue">{{ $data->bet_odds  }}</span><br>{{ $data->bet_odds_letter}}</td>
            <td style="text-align:center"><span style="color:blue">{{ $data->bet_stake  }}</span></td>
            
            <td>{{ $data->player_win_lose  }} <br >
            {{ $data->event_final_scores }}
            </td>            
              
          </tr>
          @endforeach
          @else
          <tr><td colspan="6">No data!</td></tr>
        @endif
        </table>
    </div>
@endsection
@section('scripts')
  <script>
	$(document).ready(function(){
			$('#ondate').change(function(){
				$('#formSearch').submit();
			});
      $('.datepicker').datepicker();
	});
  </script>

@stop
