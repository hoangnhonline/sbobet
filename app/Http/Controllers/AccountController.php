<?php namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Models\Match;
use App\Models\Ratio;
use App\Models\ScheduleBet;
use App\Models\League;
use App\Models\MatchBet;
use App\Models\AccountLeague;
use App\Models\PriceRange;
use App\Models\Account;
use Session;
use DB;

class AccountController extends Controller {

	public static $provider ;
	public static $account_id ;

	public function __construct(Match $matchModel)
	{	
			
		$this->middleware('admin');
		
		self::$provider = session('provider')!= NULL ? session('provider') : 2;
		self::$account_id = session('account_id');
		$detailAccount = Account::find(self::$account_id);
		$accountArr = Account::where('provider', self::$provider)->where('status', 1)->get();		

		view()->share([ 'accountArr' => $accountArr, 'provider' => self::$provider, 'detailAccount' => $detailAccount]);


	}	
	public function manage(Request $request)
	{	
		$detail = (object) [];
		
		$id = $request->id; 
		
		$detail = Account::find($id);
		$oldArr = Account::where(['status' => 0, 'provider' => self::$provider])->get();

		return view('back.account.manage-account', compact('detail', 'id', 'oldArr'));
	}
	public function change(Request $request){
		$account_type = $request->account_type;

		if( $account_type == 1) { // new
			$model = new Account;
			$model->username = $request->username;
			$model->password = $request->password;
			$model->user_alias = $request->user_alias;
			$model->proxy = $request->proxy;
			$model->status = 1;
			$model->provider = $request->provider;
			$model->save();

			
		}else{
			$old_id = $request->old_id;
			$modelOld = Account::find($old_id);
			$modelOld->status = 1;
			$modelOld->user_alias = $request->user_alias;
			$modelOld->proxy = $request->proxy;
			$modelOld->save();
		}

		$change_id = $request->change_id;
		$modelChange = Account::find($change_id);
		$modelChange->status = 0;
		$modelChange->user_alias = '';
		$modelChange->save();
		Session::flash('flash_message', 'Change account success!');
		return redirect()->route('manage-account');
	}
	public function store(Request $request){
		$dataArr = $request->all();
		Account::create($dataArr);
		Session::flash('flash_message', 'Add account success!');
		return redirect()->route('manage-account');
	}
	public function destroy(Request $request)
	{	
		$id = $request->id;
		Account::destroy($id);
//		Match::where('user_id', $id)->delete();
		//MatchBet::where('user_id', $id)->delete();
		//ScheduleBet::where('account_id', $id)->delete();
		//AccountLeague::where('account_id', $id)->delete();
		return redirect()->route('manage-account');
	}
	public function deactive(Request $request)
	{	
		$id = $request->id;
		$model = Account::find($id);
		$model->status = 0;
		$model->save();
		Session::flash('flash_message', 'Deactive account success!');
		return redirect()->route('manage-account');
	}
	public function active(Request $request)
	{	
		$id = $request->id;
		$model = Account::find($id);
		$model->status = 1;
		$model->save();
		Session::flash('flash_message', 'Active account success!');
		return redirect()->route('manage-account');
	}
	public function create(Request $request){

		$oldArr = Account::where(['status' => 0, 'provider' => self::$provider])->get();
		$provider = self::$provider;
		Session::flash('flash_message', 'Delete account success!');
		return view('back.account.create', compact('oldArr', 'provider'));

	}
}
