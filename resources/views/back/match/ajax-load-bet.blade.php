@if(Session::has('flash_message'))
      <div class="alert alert-success">
          {{ Session::get('flash_message') }}
      </div>
  @endif
    <div class="table-responsive " >
    <div class="{{ $matchDetail->time_in_half == 1 ? "col-md-8" : "col-md-12" }}" style="padding-left:0px !important;padding-right:0px !important;">
    <table class="table table-bordered" id="current">
    <tr>
      <th rowspan="2">Time</th>
      <th rowspan="2">Event</th>      
      <th colspan="6">Full Time</th>      
    </tr>
    <tr>
      <th>HDP</th>
      <th>H</th>
      <th>A</th>
      <th>Goal</th>
      <th>Over</th>
      <th>Under</th>
    </tr>
    @if(!empty($matchBetType1))
      @foreach($matchBetType1 as $k => $v)
      <tr>
        <td>{{ $matchDetail->status != 3 ? strip_tags($matchDetail->time) : "End time" }}  ::  <span style="color:red">{{ $matchDetail->score }} - {{ $matchDetail->score2 }}</span></td>
        <td>{{ $matchDetail->team_name }} - {{$matchDetail->team_name2 }}</td>
        <td style="text-align:center">{{ $v->bet_ratio }}</td>
        <td style="text-align:center">{{ $v->bet_ratio2  }}</td>
        <td style="text-align:center">{{ $v->bet_ratio3  }}</td>
        <td style="text-align:center">{{ isset($matchBetType3[$k]) ? $matchBetType3[$k]->bet_ratio : "" }}</td>
        <td style="text-align:center">{{ isset($matchBetType3[$k]) ? $matchBetType3[$k]->bet_ratio2 : "" }}</td>
        <td style="text-align:center">{{ isset($matchBetType3[$k]) ? $matchBetType3[$k]->bet_ratio3 : "" }}</td>
      </tr>
      @endforeach
    @endif
    </table>
    </div>
	@if($matchDetail->time_in_half == 1)
    <div class="col-md-4" style="padding-left:0px !important;padding-right:0px !important;">
      <table class="table table-bordered" id="current">
        <tr>
            
          <th colspan="6">First Half</th>      
        </tr>
        <tr>
          <th>HDP</th>
          <th>H</th>
          <th>A</th>
          <th>Goal</th>
          <th>Over</th>
          <th>Under</th>
        </tr>
        @if(!empty($matchBetType7))
          @foreach($matchBetType7 as $k1 => $v1)
          <tr>          
            <td style="text-align:center">{{ $v1->bet_ratio }}</td>
            <td style="text-align:center">{{ $v1->bet_ratio2  }}</td>
            <td style="text-align:center">{{ $v1->bet_ratio3  }}</td>
            <td style="text-align:center">{{ isset($matchBetType9[$k1]) ? $matchBetType9[$k1]->bet_ratio : "" }}</td>
            <td style="text-align:center">{{ isset($matchBetType9[$k1]) ? $matchBetType9[$k1]->bet_ratio2 : "" }}</td>
            <td style="text-align:center">{{ isset($matchBetType9[$k1]) ? $matchBetType9[$k1]->bet_ratio3 : "" }}</td>
          </tr>
          @endforeach
        @endif
        </table>
    </div>
	@endif
    <div class="clearfix"></div>
     @if($lichKeoArr->count() > 0)
    <h4>Handicap schedule list</h4>
   
      <table class="table table-bordered" id="keo_bet">
        <thead>
          <tr>        
		  <th style="text-align:left;width:1%">ID</th>  
            <th style="text-align:left;width:100px">Bet Type</th>    
            <th style="text-align:left;width:250px">Your choice</th>
            <th style="text-align:center">Half</th>           
            <th style="text-align:center">Minutes From - To</th>
            <th style="text-align:right">Price From - To</th>
            <th style="text-align:center">Bet Portion</th>
            <th style="text-align:right">Bet Amount</th>
            <th style="text-align:right">Status</th>
            <th width="1%"></th>
          </tr>
        </thead>
        <tbody>          
          @foreach ($lichKeoArr as $lich)            
            <tr>          
			<td>{{ $lich->id }}</td>			
            <td>{{ $lich->bet_type == '7' ? 'First Half' : "Ful time" }}</td>
              <td>{{ $lich->priority == 'h' ? $matchDetail->team_name : $matchDetail->team_name2 }}</td>
              <td style="text-align:center">{{ $lich->time_half }}</td>       
              <td style="text-align:center">{{ $lich->time_from }} - {{ $lich->time_to }}</td>
              <td style="text-align:right">[ {{ $lich->ratio_from }} ] - [ {{ $lich->ratio_to }} ]</td>
              <td style="text-align:center">{{ $lich->ratio }}</td>
              <td style="text-align:right">{{ number_format($lich->amount) }}</td>
              <td style="text-align:right">
                @if($lich->status == 1)
                <span class="label label-info">Schedule</span>
                @elseif($lich->status == 2)
                <span class="label label-success">Bet success</span>                
        @elseif($lich->status == 3)
                <span class="label label-danger">Bet error</span>
        
                @elseif($lich->status == 4)
        <?php 
        $tmp1 = json_decode($lich->real_message, true);   
        ?>
                <span class="label label-danger">{{ $lich->real_message != null ? $tmp1['subTitle'] : "COULD NOT BET" }}</span>
        
        @elseif($lich->status == 5)
        <?php 
              $tmp1 = json_decode($lich->real_message, true);             
            ?>
                <span class="label label-danger">{{ $lich->real_message != null ? $tmp1['subTitle'] : "" }}</span>
        
                @endif
        <br />
        @if($lich->real_message)
          <?php 
            $tmp = json_decode($lich->real_message, true);
            echo $tmp['content'];
          ?>
        @endif  
              </td>
              <td style="white-space:nowrap">
                <a class="btn btn-info copySchedule btn-sm" data-value="{{ $lich->id }}">Copy</a>
                @if($lich->status == 1)
                <a href="{!! route('match.destroy-bet', [$matchDetail->ref_id, $lich->id]) !!}" onclick="return confirm('Are you sure you want to delete ?');" class="btn btn-danger btn-sm">Delete</a>     
                @endif
              </td>
            </tr>            
          @endforeach
          
        </tbody>
      </table>
      @endif

       @if($lichOverArr->count() > 0)
    <h4>Under/Over schedule list</h4>
   
      <table class="table table-bordered" id="keo_bet">
        <thead>
          <tr>   
<th width="1%">id</th>		  
            <th style="text-align:left;width:100px">Bet Type</th>
            <th style="text-align:left;width:250px">Your choice</th>
            <th style="text-align:center">Half</th> 
            <th style="text-align:center">Minutes From - to</th>
            <th style="text-align:right">Price From - To</th>          
            <th style="text-align:center">Bet Portion</th>
            <th style="text-align:right">Bet amount</th>
            <th style="text-align:right">Status</th>
            <th width="1%"></th>
          </tr>
        </thead>
        <tbody>          
          @foreach ($lichOverArr as $lich)            
            <tr>      
			 <td>{{ $lich->id }}</td>
              <td>{{ $lich->bet_type == '9' ? 'First Half' : "Full time" }}</td>
              <td>{{ $lich->priority == 'h' ? 'Over' : 'Under' }}</td>
              <td style="text-align:center">{{ $lich->time_half }}</td>
              <td style="text-align:center">{{ $lich->time_from }} - {{ $lich->time_to }}</td>
              <td style="text-align:right">[ {{ $lich->ratio_from }} ] - [ {{ $lich->ratio_to }} ]</td>
              <td style="text-align:center">{{ $lich->ratio }}</td>
              <td style="text-align:right">{{ number_format($lich->amount) }}</td>
              <td style="text-align:right">
                @if($lich->status == 1)
                <span class="label label-info">Schedule</span>
                @elseif($lich->status == 2)
                <span class="label label-success">Bet success</span>                
        @elseif($lich->status == 3)
                <span class="label label-danger">Bet error</span>
        
                @elseif($lich->status == 4)
        <?php 
        $tmp1 = json_decode($lich->real_message, true);   
        ?>
                <span class="label label-danger">{{ $lich->real_message != null ? $tmp1['subTitle'] : "COULD NOT BET" }}</span>
        
        @elseif($lich->status == 5)
        <?php 
              $tmp1 = json_decode($lich->real_message, true);             
            ?>
                <span class="label label-danger">{{ $lich->real_message != null ? $tmp1['subTitle'] : "" }}</span>
        
                @endif
        <br />
        @if($lich->real_message)
          <?php 
            $tmp = json_decode($lich->real_message, true);
            echo $tmp['content'];
          ?>
        @endif  
              </td>
              <td  style="white-space:nowrap">
                <a class="btn btn-info copySchedule btn-sm" data-value="{{ $lich->id }}">Copy</a>
                @if($lich->status == 1)
                <a href="{!! route('match.destroy-bet', [$matchDetail->ref_id, $lich->id]) !!}" onclick="return confirm('Are you sure you want to delete ?');" class="btn btn-danger btn-sm">Delete</a>     
                @endif
              </td>
            </tr>            
          @endforeach
          
        </tbody>
      </table>
@endif
    </div>