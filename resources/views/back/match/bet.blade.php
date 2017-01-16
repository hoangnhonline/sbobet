@extends('back.template')

@section('main')
<style>
h4{color:red}
table#current th{
	text-align:center;
	background-color:#6787bb;
	color:#FFF;
	padding: 2px;
}
table#keo_bet th{
	
	text-align:center;
	background-color:#ffddd2;	
}
table#keo_bet td{
	padding: 2px;
}
</style>
<div class="col-md-12" style="margin-bottom:20px;padding:0px">
<a class="btn btn-default" href="{{ route('match.index')}}">Back</a>
</div>
<div class="col-lg-12" style="padding:0px"  id="loadData">
	@include('back.match.ajax-load-bet')
</div> <!--loadData-->

  <p></p>
  @if($matchDetail->status != 3)
<div class="col-md-12"  style="padding:0px" >
	<h4 class="col-md-12"  style="padding:0px" >Setup schedule </span></h4>
</div>
<div class="col-md-12"  style="padding:0px" >
	<div class="col-md-12"  style="padding:0px" >
		@if($errors->any())
		    <div class="alert alert-danger">
		        @foreach($errors->all() as $error)
		            <p>{{ $error }}</p>
		        @endforeach
		    </div>
		@endif
	</div>	
	{!! Form::open(['url' => 'match/store-bet', 'method' => 'post', 'id' => 'formSetBet']) !!}	
	<input type="hidden" name="provider" value="{{ $matchDetail->provider }}">
	<input type="hidden" name="match_id" value="{{ $matchDetail->ref_id }}">	
	  <div class="form-group col-md-6"  style="padding-left:0px" >
	    <label for="pwd">Bet Type</label>
	    <select class="form-control" name="bet_type" id="bet_type">
		    <option value="1" {{ $bet_type == 1 ? "selected" : "" }}>Handicap Full Time </option>
		    <option value="3" {{ $bet_type == 3 ? "selected" : "" }}>Under/Over Full Time</option>
			
		    <option value="7" {{ $bet_type == 7 ? "selected" : "" }}>Handicap First Half</option>
		    <option value="9" {{ $bet_type == 9 ? "selected" : "" }}>Under/Over First Half</option>
			
	    </select>
	  </div>
	  @if( $bet_type == 1 || $bet_type == 7)

	  <div class="form-group col-md-6"  style="padding-right:0px" >
	    <label for="pwd">Bet Portion</label>
	    <select class="form-control selectpicker show-tick" data-live-search="true" name="ratio">
	    	<option value="">All</option>
	    	@foreach($ratioArr as $ratio)
		    <option value="{{ $ratio->value }}" {{ old('ratio') == $ratio->value ? "selected" : "" }}>{{ $ratio->value }}</option>
		    @endforeach
	    </select>
	  </div>
	  <div class="form-group col-md-6"  style="padding:0px" >
	    <label for="email">Choice Team</label>
	    <select class="form-control" name="priority">
	    	<option value="h" {{ old('priority') == 'h' ? "selected" : "" }}>{{ $matchDetail->team_name }}</option>
	    	<option value="a" {{ old('priority') == 'a' ? "selected" : "" }}>{{ $matchDetail->team_name2 }}</option>
	    </select>
	  </div>
	  @endif
	  @if( $bet_type == 3 || $bet_type == 9)
	  <div class="form-group col-md-6"  style="padding:0px" >
	    <label for="pwd">Bet Portion ( Bet portion = Goal - Total goal ) </label>
	    <select class="form-control selectpicker show-tick" data-live-search="true" name="ratio">
	    	<option value="">All</option>
	    	@foreach($arrUnderOver as $ratio)
		    <option value="{{ $ratio }}" {{ old('ratio') == $ratio ? "selected" : "" }}>{{ $ratio }}</option>
		    @endforeach
	    </select>
	  </div>
	  <div class="form-group col-md-6"  style="padding-left:0px" >
	    <label for="email">Choice O/U</label>
	    <select class="form-control" name="priority">
	    	<option value="a" {{ old('priority') == 'a' ? "selected" : "" }}>Under</option>
	    	<option value="h" {{ old('priority') == 'h' ? "selected" : "" }}>Over</option>	    	
	    </select>
	  </div>		  
	  @endif
	  <div class="form-group col-md-6"   >
	    <label for="email">Score</label>
	    <input type="text" name="score" class="form-control" id="score" value="{{ old('score') }}">	 
	  </div> 
	  <div class="form-group col-md-12"  style="padding:0px" >
	    <label for="pwd">Half</label>
	    <select class="form-control" name="time_half">
		    @if($matchDetail->time_in_half != "2")
			<option value="1" {{ old('time_half') == 1 ? "selected" : "" }}>1</option>
			@endif
			@if( $bet_type != 7 && $bet_type != 9)
		    <option value="2" {{ old('time_half') == 2 ? "selected" : "" }}>2</option>
		    @endif
	    </select>
	  </div>
	  <div class="form-group col-md-2"  style="padding-left:0px;padding-top:20px" >
	   
	    <input type="checkbox" name="exclude_time" id="exclude_time" value="1">	 <label for="exclude_time">All Time</label>    
	    
	  </div>
	  <div class="form-group col-md-5"  style="padding-left:0px" >
	    <label for="pwd">From (minute)</label>
	    <input type="text" name="time_from" class="form-control" id="time_from" value="{{ old('time_from') }}">	    
	    
	  </div>
	  <div class="form-group col-md-5"  style="padding-right:0px" >
	    <label for="pwd">To (minute)</label>
	    <input type="text" name="time_to" class="form-control" id="time_to" value="{{ old('time_to') }}">
	    
	  </div>
	  <div class="form-group col-md-2"  style="padding-left:0px;padding-top:20px" >
	   
	    <input type="checkbox" name="exclude_price" id="exclude_price" value="1">	 <label for="exclude_price">All Price</label>    
	    
	  </div>	  
	  <div class="form-group col-md-5"  style="padding-left:0px" >
	    <label for="pwd">From Price</label>
	    <input type="text" name="ratio_to" class="form-control" id="ratio_to" value="{{ old('ratio_to') }}">
	    
	  </div>
	  <div class="form-group col-md-5"  style="padding-right:0px" >
	    <label for="pwd">To Price</label>
	    <input type="text" name="ratio_from" class="form-control" id="ratio_from" value="{{ old('ratio_from') }}">
	    
	  </div>	  
	  <div class="form-group col-md-12"  style="padding:0px" >
	    <label for="pwd">Bet amount</label>
	    <input type="text" name="amount" class="form-control" id="amount" value="{{ old('amount') }}">
	  </div>
	  <input type="hidden" id="match_id_copy" value="" name="match_id_copy">
	  	<div class="col-md-12"  style="padding:0px" >
	  		<button type="submit" onclick="return validate();" class="btn btn-primary">Save</button>
	  		<button type="submit" onclick="return validateCopy();" class="btn btn-primary">Save and Copy</button>	  
	  	</div>
	{!! Form::close() !!}
</div>
@include('back.match.match-modal')
@endif

@stop

@section('scripts')
<script>
$(document).on('click', '.copySchedule', function(){
	$('#match_id_copy').val('');
	var schedule_id = $(this).attr('data-value');
	$.ajax({
	    url: '{{ route("ajax-match-modal") }}',
	    type: "POST",
	    async: false,      
	    data: {
	    	match_id : '{{ $matchDetail->ref_id }}',
	    	_token: "{{ csrf_token() }}",
	    	schedule_id : schedule_id        	
	    },
	   
	    success: function (response) {
	      $('#contentCopy').html(response);
	      $('#myModalCopy').modal('show');
	      $('.selectpicker').selectpicker();
	      $('.datepicker').datepicker();
	    
	    },
	    error: function(response){                             
	        
	    }
	  });
});
function validateCopy(){
	if( validate() ){
		$.ajax({
            url: '{{ route("ajax-match-modal") }}',
            type: "POST",
            async: false,      
            data: {
            	match_id : '{{ $matchDetail->ref_id }}',
            	_token: "{{ csrf_token() }}",          	
            },
           
            success: function (response) {
              $('#contentCopy').html(response);
              $('#myModalCopy').modal('show');
              $('.selectpicker').selectpicker();
              $('.datepicker').datepicker();
            
            }
          });
		
		return false;
	}
	return false;
}
function validate(){
	if($('#exclude_time').prop('checked') == false){
		var time_from = $('#time_from').val();
		var time_to = $('#time_to').val();
		if( time_from >= time_to ){
			alert('From (minute), To (minute) is invalid.');
			return false;
		}
	}
	if($('#exclude_price').prop('checked') == false){
		var ratio_from = $.trim($('#ratio_from').val());
		if( ratio_from == ''){
			alert('Please enter From Price');
			return false;
		}else{
			ratio_from = parseFloat( $('#ratio_from').val() );
		}

		var ratio_to = $.trim($('#ratio_to').val());
		if( ratio_to == ''){
			alert('Please enter To Price');
			return false;
		}else{
			ratio_to = parseFloat( $('#ratio_to').val() );
		}	

		if( ratio_from >= ratio_to ){
			alert('From Price, To Price is invalid.');
			return false;
		}
	}
	if( $.trim( $('#amount').val() ) == ''){
		alert('Please enter Bet amount.');
		return false;	
	}
	return true;
}
$(function(){
	$('#bet_type').change(function(){
		location.href="{{ route('match.bet', [$matchDetail->ref_id]) }}?bet_type=" + $(this).val();
	});	
	setInterval(function(){ 
		$.ajax({
            url: '{{ route('ajax-load-bet') }}',
            type: "GET",
            async: false,      
            data: {
            	ref_id : '{{ $matchDetail->ref_id }}'            	
            },
           
            success: function (response) {
              $('#loadData').html(response);
            },
            error: function(response){                             
                
            }
          });
	}, 20000);
});
</script>
<script type="text/javascript">
  $(document).on('click', '#btnSaveCopy', function(){
  	var schedule_id = $('#schedule_id').val();
  	var match_id_copy = $('#match_id_copy').val();
  	if( match_id_copy != '' ){
	  	if( schedule_id > 0 ){
	  		$.ajax({
	            url: '{{ route("ajax-copy-schedule") }}',
	            type: "POST",
	            async: true,      
	            data: {
	            	schedule_id : schedule_id,
	            	match_id_copy : match_id_copy,
	            	_token: "{{ csrf_token() }}"
	            },     
	            success: function (response) {
	              alert('Copy schedule success! Please check it.');
	              window.location.reload();
	            }
		    });
	  	}else{
	  		$('#formSetBet').submit();	
	  	}
  	}else{
  		alert('Please choose at least one match to copy schedule.');
  		return false;
  	}
  	
  });
  $(document).on('click', '#btnFilterAjax', function(){
    filterAjax();
  });  
  $(document).on('change', '#ondate_ajax, #status_ajax, #league_id_ajax', function(){
    filterAjax();
  });
  $(document).on('click', '.checkboxCopy', function(){
  	var obj = $(this);
  	var match_id_copy = $('#match_id_copy').val();
  	if(obj.prop('checked') == true){
  		match_id_copy += obj.val() + ',';
  	}else{
  		var str = obj.val() + ',';
		match_id_copy = match_id_copy.replace(str, '');
  	}
  	$('#match_id_copy').val(match_id_copy);
  });
  function filterAjax(){

		$.ajax({
            url: '{{ route("ajax-match-modal") }}',
            type: "POST",
            async: true,      
            data: $('#formSearchAjax').serialize(),
            beforeSend:function(){
              $('#contentCopy').html('<div style="text-align:center"><img src="{{ URL::asset('img/loading.gif')}}"></div>');
            },        
            success: function (response) {
              $('#contentCopy').html(response);
              $('#myModalCopy').modal('show');
              $('.selectpicker').selectpicker();
              $('.datepicker').datepicker();
              //check lai nhung checkbox da checked
              var str_checked = $('#match_id_copy').val();
              tmpArr = str_checked.split(",");
              for (i = 0; i < tmpArr.length; i++) { 
				    $('.checkboxCopy[value='+ tmpArr[i] +']').prop('checked', true);
			  }
            }
	    });
	}
  </script>
@stop
