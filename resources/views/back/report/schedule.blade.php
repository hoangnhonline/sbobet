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
          <div class="form-group"  >
            <label for="pwd">Bet Type</label><br/ >
            <select class="form-control" name="bet_type" id="bet_type">
              <option value="">--All--</option>
              <option value="1" {{ $bet_type == 1 ? "selected" : "" }}>Handicap Full Time </option>
              <option value="3" {{ $bet_type == 3 ? "selected" : "" }}>Under/Over Full Time</option>
              <option value="7" {{ $bet_type == 7 ? "selected" : "" }}>Handicap First Half</option>
              <option value="9" {{ $bet_type == 9 ? "selected" : "" }}>Under/Over First Half</option>
            </select>
          </div>
          <div class="form-group"  >
            <label for="pwd">Status</label><br/ >
            <select class="form-control" name="status" id="status">
              <option value="">--All--</option>
              <option value="1" {{ $status == 1 ? "selected" : "" }}>Schedule</option>
              <option value="2" {{ $status == 2 ? "selected" : "" }}>Bet success</option>
              <option value="3" {{ $status == 3 ? "selected" : "" }}>Error and Other</option>              
            </select>
          </div>
        <button type="submit" class="btn btn-primary" style="margin-top:25px">Submit</button>
      </form>
    </div>
  </div>
 <div class="clearfix"></div>
  <h3 style="padding-left:0px;text-align:left">Schedule report : {{ $ondate }} [{{ $dataArr->count() }}]</h3>
 @if($status != 1)
 <h4>Right conditions : <span style="color:blue">{{ $thoa }}</span> 
  @if(!$status)
  - Bet success : {{ $success }} @if($thoa >0 )(<span style="color:red">{{ ceil($success*100/$thoa) }}%</span>)@endif
	@endif
</h4>
@if($status)
  <h4>Wrong conditions : <span style="color:red">{{ $khongthoa }}</span></h4>
@endif
  @endif
   <div class="col-md-12" style="padding:0px !important;"> 
      <table class="table table-bordered" id="current">
        <tr>
          <th width="1%">ID</th>
          <th>Match</th>
		  @if($status > 1)
		  <th>Right conditions</th>
			@endif
          <th>Bet Type</th>                
          <th>Selection</th>      
          <th>Time / price</th>      
          <th style="text-align:right">Bet amount</th>           
          <th width="200px">Status</th>           
        </tr>    
        @if(!empty($dataArr))
        <?php $i = 0; ?>
          @foreach($dataArr as $data)
          <?php $i++; ?>
          <tr>        
            <td>{{ $data->id }}</td>
            <td>{{ $data->match_id }}<br/>{{ !empty($detailMatch[$data->match_id]) ? $detailMatch[$data->match_id]['team_name'] : '...' }} - {{ !empty($detailMatch[$data->match_id]) ? $detailMatch[$data->match_id]['team_name2'] : "..." }}
			<?php 
			
			?>
              
              <br/><a href="{{ route('view-log', ['schedule_id' => $data->id]) }}" target="_blank">View log</a>
             
            </td>
			@if($status > 1)
			<td style="text-align:center">
				<?php 
				if($thoaArr[$data->id] > 0){
					echo "<h1 style='color:red'>".$thoaArr[$data->id]."</h1>";
				}else{
					echo 0;
				}
				?>
			</td>
			@endif
            <td>
              <?php 
              if( $data->bet_type== 1){
                echo "Handicap Full Time";
              }elseif( $data->bet_type == 3){
                echo "Under/Over Full Time";
              }elseif( $data->bet_type == 7){
                echo "Handicap First Half";
              }else{
                echo "Under/Over First Half";
              }
              ?>
            </td>
            <td>
              @if( $data->bet_type == 1 ||  $data->bet_type == 7)
                @if( $data->priority == "h")
                  {{ $data->team_name }}
                @else
                  {{ $data->team_name2 }}
                @endif
              @else
                @if( $data->priority == "h")
                  Over
                @else
                  Under
                @endif
              @endif

            </td>
            <td style="text-align:right">
				Half : {{ $data->time_half }}<br>
              <span style="color:red;font-weight:bold">{{ $data->ratio }}</span><br>
              {{ $data->time_from }} - {{ $data->time_to }} <br>
              [ {{ $data->ratio_to }} - {{ $data->ratio_from }} ]              
              <br > 
            </td>
            <td style="text-align:right">
              {{ $data->amount }}
            </td>            
            <td width="300px" style="font-size:12px">
              @if($data->status == 1)
                <span class="label label-info">Schedule</span>
                @elseif($data->status == 2)
                <span class="label label-success">Bet success</span>                
                @elseif($data->status == 3)
                        <span class="label label-danger">Bet error</span>
                
                        @elseif($data->status == 4)
                <?php 
                $tmp1 = json_decode($data->real_message, true);   
                ?>
                        <span class="label label-danger">{{ $data->real_message != null ? $tmp1['subTitle'] : "COULD NOT BET" }}</span>
                @if($data->real_message == 'Please wait while your bet is being processed' )
	<span class="label label-warning">Waiting confirm</span><br />Response message : {{ $data->real_message }}
@else
Response message : {{ $data->real_message }}
@endif
                @elseif($data->status == 5)
                <?php 
                      $tmp1 = json_decode($data->real_message, true);             
                    ?>
                        <span class="label label-danger">{{ $data->real_message != null ? $tmp1['subTitle'] : "" }}</span>
                 @elseif($data->status == 7)
               
                        Response message : {{ $data->real_message != null ? $data->real_message : "" }}
                        @endif
                <br />
                @if($data->real_message)
                  <?php 
                    $tmp = json_decode($data->real_message, true);
                    echo $tmp['content'];
                  ?>
                @endif  
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
			$('#ondate, #bet_type, #status').change(function(){
				$('#formSearch').submit();
			});
      $('.datepicker').datepicker({
        dateFormat : 'yy-mm-dd'
      });
	});
  </script>

@stop
