@extends('back.template')

@section('main')
<div class="clearfix"></div>
  @if(Session::has('flash_message'))
      <div class="alert alert-success">
          {{ Session::get('flash_message') }}
      </div>
  @endif

<div class="clearfix"></div>
<a href="{{ route('account.create') }}" class="btn btn-info btn-sm">Add account</a>
<div class="clearfix"></div>
  @if( $id > 0)
    <div class="col-md-6"  style="padding-left:0px !important;" >
      <h3>Change Account : <span style="color:red">{{ $detail->username }}</span></h3>
        <form method="POST" action="{{ route('change-account')}}">
          <div class="form-group col-md-12"  style="padding-left:0px" >
          <label for="pwd">Type</label>
          <select class="form-control" name="account_type" id="account_type">
            <option value="1" >New account</option>
            <option value="2" >Deactive account</option>
          </select>
        </div>
        <div id="info_new">
          <div class="form-group col-md-12"  style="padding-left:0px" >
            <label for="pwd">Username</label>
            <input type="text" name="username" class="form-control" id="username" value="{{ old('username') }}">     
            
          </div>
           <div class="form-group col-md-12"  style="padding-left:0px" >
            <label for="pwd">Password</label>
            <input type="text" name="password" class="form-control" id="password" value="{{ old('password') }}">     
            
          </div>
        </div>
        <input type="hidden" name="user_alias" value="{{ $detail->user_alias }}">
        <input type="hidden" name="proxy" value="{{ $detail->proxy }}">
        <input type="hidden" name="status" value="1">
        <div id="info_old" style="display:none">  
          <div class="form-group col-md-12"  style="padding-left:0px;" >
            <label for="pwd">Choice account</label>
            <select name="old_id" class="form-control" id="old_id">
              <option value="0">--choice---</option>
              @if( $oldArr->count() > 0)
                @foreach( $oldArr as $old )
                  <option value="{{ $old->id }}">{{ $old->username }} / {{ $old->password }}</option>
                @endforeach
              @endif
            </select>
            
          </div>
          
        </div>
        <div class="col-md-12"  style="padding:0px" >
            <button type="submit" onclick="return validate();" class="btn btn-primary">Save</button>        
            <a href="{{ route('manage-account') }}" class="btn btn-default">Cancel</a>        
          </div>
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="change_id" value="{{ $detail->id }}">
          <input type="hidden" name="provider" value="{{ $detail->provider }}">
      </form>
    </div>
    @endif
        <div class="clearfix"></div>
  <h3 style="padding-left:0px;text-align:left">Active account</h3>
   <div class="col-md-10" style="padding:0px !important;"> 
      <table class="table table-bordered" id="current">
        <tr>
          <th width="1%">Id</th>
          <th>Username</th>      
          <th>Password</th>      
          <th>Alias</th>
          <th>Proxy</th>
          <th>Last updated</th> 
          <th width="200px">Action</th>
        </tr>    
        @if(!empty($accountArr))
          @foreach($accountArr as $account)
          <tr>        
            <td>{{ $account->id }}</td>
            <td>{{ $account->username  }}</td>
            <td><?php 
              $len = strlen($account->password);
              for( $i = 0; $i<=$len; $i++){
                echo "*";
              }

            ?></td>
            <td>{{ $account->user_alias  }}</td>
            <td>{{ $account->proxy  }}</td>
            <td>{{ $account->updated_at  }}</td> 
            <td><a href="{{ route('manage-account', ['id' => $account->id] ) }}" class="btn btn-warning btn-sm">Change</a>
              <a href="{{ route('account.deactive', $account->id) }}" onclick="return confirm('Are you sure you want to deactive ?');" class="btn btn-danger btn-sm">Deactive</a>
            </td>
            
          </tr>
          @endforeach
          @else
          <tr><td colspan="5">No data!</td></tr>
        @endif
        </table>
    </div>
    <div class="clearfix"></div>

    <div class="clearfix"></div>
  <h3 style="padding-left:0px;text-align:left">Deactive account</h3>
   <div class="col-md-6" style="padding:0px !important;"> 
      <table class="table table-bordered" id="current">
        <tr>
          <th width="1%">Id</th>
          <th>Username</th>      
          <th>Password</th>      
          <th>Alias</th>
          <th>Proxy</th>  
          <th>Last updated</th>
          <th width="1%">Action</th>                
        </tr>    
        @if(!empty($oldArr))
          @foreach($oldArr as $account)
          <tr>        
            <td>{{ $account->id }}</td>
            <td>{{ $account->username  }}</td>
            <td><?php 
              $len = strlen($account->password);
              for( $i = 0; $i<=$len; $i++){
                echo "*";
              }

            ?></td>
            <td>{{ $account->user_alias  }}</td>
            <td>{{ $account->proxy  }}</td>
            <td>{{ $account->updated_at  }}</td>           
            <td>
            @if(!$account->user_alias)
            <a href="{{ route('account.destroy', $account->id) }}" onclick="return confirm('Are you sure you want to delete ?');" class="btn btn-danger btn-sm">Delete</a>
            @else
            <a href="{{ route('account.active', $account->id) }}" onclick="return confirm('Are you sure you want to active ?');" class="btn btn-primary btn-sm">Active</a>
            @endif
            </td>           
          </tr>
          @endforeach
        @endif
        </table>
    </div>
    <div class="clearfix"></div>

    <div style="height:200px"></div>
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
  }else{
    if( $('#old_id').val() == 0){
      alert('Please choice old account!'); return false; 
    }
  }
}
  $(document).ready(function(){
    $('#account_type').change(function(){
      var val = $(this).val();
      if( val == 1){
        $('#info_new').show();
        $('#info_old').hide();
      }else{
        $('#info_new').hide();
        $('#info_old').show();
      }
    });
  });

</script>
@endsection