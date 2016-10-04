@extends('back.template')

@section('main')
<div class="clearfix"></div>
 <div class="clearfix"></div>
  <h3 style="padding-left:0px;text-align:left">Your choice : <span style="color:red">{{ $s->priority == 'h' ? "Over" : "Under" }} - {{ $s->ratio }} [ {{ $s->ratio_to }} - {{ $s->ratio_from }}  ]</span></h3>

   <div class="col-md-6" style="padding:0px !important;"> 
      @if( $haveLog > 0)
      <table class="table table-bordered" id="current" width="500px">
        <tr>
<th width="1%">ID</th>
        <th width="1%">Half</th>
          <th width="1%">Minute</th>
		  <th>Score</th>
          <th>Ratio</th>
          <th>Over</th>                
          <th>Under</th>                
        </tr>    
        @if($dataArr->count() > 0)
        <?php $i = 0; ?>
          @foreach($dataArr as $data)
          <?php $i++; ?>
          <tr>    <td>{{ $data->id }}</td>    
            <td>{{ $data->half }}</td>
            <td>{{ $data->minute }}</td>
			<td>
              {{ $data->score }} - {{ $data->score2 }}
            </td>
            <td>
              {{ $data->bet_ratio }}
            </td>
            <td>
               <span>{{ $data->bet_ratio2 }}</span>
            </td>
            <td>  
             <span>{{ $data->bet_ratio3 }}</span>

            </td>
          </tr>
          @endforeach
          @else
          <tr><td colspan="7">No record with bet ratio = {{ $s->ratio }} [ {{ $s->time_from  }} -> {{ $s->time_to }} ].</td></tr>
        @endif
        </table>
        @else
        <h3>Sorry, this match has not recorded history.</h3>
        @endif

        <p>match_id : <strong>{{ $s->match_id }}</strong>
        <br>user_id : <strong>{{ $s->account_id }}</p></strong>
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
