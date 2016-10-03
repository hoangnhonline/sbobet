@if(Session::has('flash_message'))
	    <div class="alert alert-success">
	        {{ Session::get('flash_message') }}
	    </div>
	@endif
    <div class="table-responsive">
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
				<td>{{ $matchDetail->status != 3 ? strip_tags($matchDetail->time) : "Đã kết thúc" }} </br> <span style="color:red">{{ $matchDetail->score }} - {{ $matchDetail->score2 }}</span></td>
				<td>{{ $matchDetail->team_name }} <br />{{$matchDetail->team_name2 }}</td>
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
	   @if($lichKeoArr->count() > 0)
	  <h4>Lịch cược KÈO đã đặt</h4>
	 
      <table class="table table-bordered" id="keo_bet">
        <thead>
          <tr>            
            <th style="text-align:left">Chọn đội</th>
            <th style="text-align:left">Hiệp thi đấu</th> 					
            <th style="text-align:left">Phút</th>
            <th style="text-align:right">Giá tiền</th>
            <th style="text-align:left">Kèo</th>
            <th style="text-align:right">Số tiền</th>
            <th style="text-align:right">Trạng thái</th>
            <th width="1%"></th>
          </tr>
        </thead>
        <tbody>          
          @foreach ($lichKeoArr as $lich)            
            <tr>              
              <td>{{ $lich->priority == 'h' ? $matchDetail->team_name : $matchDetail->team_name2 }}</td>
              <td>Hiệp {{ $lich->time_half }}</td>			  
              <td>{{ $lich->time_from }} - {{ $lich->time_to }}</td>
              <td style="text-align:right">[ {{ $lich->ratio_from }} ] - [ {{ $lich->ratio_to }} ]</td>
              <td>{{ $lich->ratio }}</td>
              <td style="text-align:right">{{ number_format($lich->amount) }}</td>
              <td style="text-align:right">
              	@if($lich->status == 1)
              	<span class="label label-info">Chưa cược</span>
              	@elseif($lich->status == 2)
              	<span class="label label-success">Cược thành công</span>              	
				@elseif($lich->status == 3)
              	<span class="label label-danger">Cược thất bại</span>
				
              	@elseif($lich->status == 4)
				<?php 
				$tmp1 = json_decode($lich->real_message, true);		
				?>
              	<span class="label label-danger">{{ $lich->real_message != null ? $tmp1['subTitle'] : "Trận đấu kết thúc" }}</span>
				
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
              <td>
              	@if($lich->status == 1)
              	<a href="{!! route('match.destroy-bet', [$matchDetail->ref_id, $lich->id]) !!}" onclick="return confirm('Chắc chắn xóa lịch cược này ?');" class="btn btn-danger btn-sm">Delete</a>     
              	@endif
              </td>
            </tr>            
          @endforeach
          
        </tbody>
      </table>
     	@endif

     	 @if($lichOverArr->count() > 0)
	  <h4>Lịch cược Under/Over đã đặt</h4>
	 
      <table class="table table-bordered" id="keo_bet">
        <thead>
          <tr>            
            <th style="text-align:left">Lựa chọn</th>
            <th style="text-align:left">Phút</th>
            <th style="text-align:right">Giá tiền</th>
            <th style="text-align:left">Kèo</th>
            <th style="text-align:right">Số tiền</th>
            <th style="text-align:right">Trạng thái</th>
            <th width="1%"></th>
          </tr>
        </thead>
        <tbody>          
          @foreach ($lichOverArr as $lich)            
            <tr>              
              <td>{{ $lich->priority == 'h' ? 'Over' : 'Under' }}</td>
              <td>{{ $lich->time_from }} - {{ $lich->time_to }}</td>
              <td style="text-align:right">[ {{ $lich->ratio_from }} ] - [ {{ $lich->ratio_to }} ]</td>
              <td>{{ $lich->ratio }}</td>
              <td style="text-align:right">{{ number_format($lich->amount) }}</td>
              <td style="text-align:right">
              	@if($lich->status == 1)
              	<span class="label label-info">Chưa cược</span>
              	@elseif($lich->status == 2)
              	<span class="label label-success">Cược thành công</span>              	
				@elseif($lich->status == 3)
              	<span class="label label-danger">Cược thất bại</span>
				
              	@elseif($lich->status == 4)
				<?php 
				$tmp1 = json_decode($lich->real_message, true);		
				?>
              	<span class="label label-danger">{{ $lich->real_message != null ? $tmp1['subTitle'] : "Trận đấu kết thúc" }}</span>
				
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
              <td>
              	@if($lich->status == 1)
              	<a href="{!! route('match.destroy-bet', [$matchDetail->ref_id, $lich->id]) !!}" onclick="return confirm('Chắc chắn xóa lịch cược này ?');" class="btn btn-danger btn-sm">Delete</a>     
              	@endif
              </td>
            </tr>            
          @endforeach
          
        </tbody>
      </table>
     	@endif
    </div>