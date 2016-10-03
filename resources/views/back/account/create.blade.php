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
<a class="btn btn-default" href="{{ route('manage-account')}}">Back</a>
</div>
<div class="col-md-12"  style="padding:0px" >
	<h4 class="col-md-12"  style="padding:0px" >Add account</span></h4>
</div>
<div class="col-md-8"  style="padding:0px" >
	<div class="col-md-12"  style="padding:0px" >
		@if($errors->any())
		    <div class="alert alert-danger">
		        @foreach($errors->all() as $error)
		            <p>{{ $error }}</p>
		        @endforeach
		    </div>
		@endif
	</div>
	<form method="POST" action="{{ route('store-account')}}">
         
        <div id="info_new">
          <div class="form-group col-md-12"  style="padding-left:0px" >
            <label for="pwd">Username</label>
            <input type="text" name="username" class="form-control" id="username" value="{{ old('username') }}">     
            
          </div>
           <div class="form-group col-md-12"  style="padding-left:0px" >
            <label for="pwd">Password</label>
            <input type="text" name="password" class="form-control" id="password" value="{{ old('password') }}">     
            
          </div>
          <div class="form-group col-md-12"  style="padding-left:0px" >
            <label for="pwd">Proxy</label>
            <input type="text" name="proxy" class="form-control" id="proxy" value="{{ old('proxy') }}">     
            
          </div>
        </div>        
        <input type="hidden" name="status" value="0">
        <div class="col-md-12"  style="padding:0px" >
            <button type="submit" onclick="return validate();" class="btn btn-primary">Save</button>        
            <a href="{{ route('manage-account') }}" class="btn btn-default">Cancel</a>        
          </div>
          <input type="hidden" name="_token" value="{{ csrf_token() }}">          
          <input type="hidden" name="provider" value="{{ $provider }}">
      </form>
</div>

@stop

@section('scripts')
<script type="text/javascript">
function validate(){
  var type = $('#account_type').val();
  if( type == 1){
    if( $.trim($('#username').val()) == ''){
      alert('Please enter username!'); return false;
    }
    if( $.trim($('#password').val()) == ''){
      alert('Please enter password!'); return false;
    }
    if( $.trim($('#proxy').val()) == ''){
      alert('Please enter proxy!'); return false;
    }
  }else{
    if( $('#old_id').val() == 0){
      alert('Please choice old account!'); return false; 
    }
  }
} 

</script>

@stop
