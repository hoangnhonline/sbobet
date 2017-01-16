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

class MatchController extends Controller {

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
	public function listAccount()
	{
		return view('back.match.set-account');
	}

	public function league(Request $request){
		
		$name = $request->name ? trim($request->name) : "";

		$selectedId = $arrLeagueSelected = [];

		$account_id = self::$account_id; 

		$tmp = AccountLeague::where('account_id', self::$account_id)
			->leftJoin('league', 'league.league_id', '=', 'account_league.league_id')
			->where('league.provider', self::$provider)
			->select('league.league_id as league_id', 'league_name')
			->orderBy('league_name')
			->get();
		if( $tmp ){
			foreach ($tmp as $key => $value) {
				$selectedId[] = $value->league_id;
				$arrLeagueSelected[] = ['league_id' => $value->league_id, 'league_name' => $value->league_name];
			}
		}		
		
		$query = DB::table('league')->where('provider', self::$provider);
		if( $name != '' ) {
			$name = strtolower($name);			
			$query->whereRaw("LOWER(league_name) LIKE '%".$name."%'");
		}

		$arrLeague = $query->orderBy('league_name')->get();		

		return view( 'back.match.league', compact('arrLeague', 'selected', 'name', 'arrLeagueAll', 'selectedId', 'arrLeagueSelected') );	
	}
	public function index(Request $request)
	{			
		$ondate = $request->ondate ? $request->ondate : date('m/d/Y');
		$strDate = date('Ymd', strtotime($ondate));	
	
		$arrLeagueId = DB::table('league')->where('provider', self::$provider)->lists('league_id');

		$a = Match::where('league_id', '>', 0)->where('provider', self::$provider)
		->where('user_id', self::$account_id)
		->groupBy('league_id')->orderBy('time_in_string')->get();

		foreach($a as $m){
		
			if($m->league_id > 0 && $m->league_name !='' && !in_array($m->league_id, $arrLeagueId) && $this->checkLeague($m->league_name) == true){
				$l = new League;
				$l->league_id = $m->league_id;
				$l->league_name = $m->league_name;
				$l->provider = $m->provider;
				$l->save();
			}		
		}
		
		//$arrLeague = DB::table('league')->where('provider', self::$provider)->lists( 'league_name', 'league_id');
		$arrLeague = AccountLeague::where('account_id', self::$account_id)
			->leftJoin('league', 'league.league_id', '=', 'account_league.league_id')
			->where('league.provider', self::$provider)
			->select('league.league_id as league_id', 'league_name')
			->get();			

		$team_name = $request->input('team_name', '');

		$league_id = $request->input('league_id', 0);

		$status = $request->input('status',2);

		$team_name = trim($team_name);
			
		$queryChuaDa = Match::where([ 'status' => $status, 'provider' => self::$provider]);

		if($league_id > 0){
				$queryChuaDa->where('league_id', '=', $league_id);
			}	
		$chuaDaArr = $queryChuaDa->whereRaw("SUBSTRING(time_in_string,1,8) = '".$strDate."'")
		->where('user_id', self::$account_id)
		->groupBy('ref_id')->orderBy('time_in_string')->get();

		if($team_name != ''){

			$queryChuaDa = Match::where([ 'status' => $status, 'provider' => self::$provider])->where('league_id', '=', $league_id);
				if($league_id > 0){
				$queryChuaDa->where('league_id', '=', $league_id);
			}	
			$queryChuaDa->where(function ($query) use ($team_name) {

		    		$query->where('team_name', '=', $team_name)		    		
		          	->orWhere('team_name2', '=', $team_name);

			});
			$queryChuaDa->where('user_id', self::$account_id);
			$queryChuaDa->whereRaw('SUBSTRING(time_in_string,8) = '.$strDate);
			$chuaDaArr = $queryChuaDa->groupBy('ref_id')->orderBy('time_in_string')->get();
		}
		//dd($chuaDaArr);
		
		//dd(DB::getQueryLog());
		$arrSchedule = [];
		if($chuaDaArr->count()){
			foreach ($chuaDaArr as $match) {
				$ref_id = $match->ref_id;
				$arrSchedule[$ref_id] = ScheduleBet::where([ 'match_id' => $ref_id, 'provider' => self::$provider, 'account_id' => self::$account_id])->count();
			}
		}
		
		
		return view('back.match.index', compact('dangDaArr', 'chuaDaArr', 'team_name', 'arrSchedule', 'arrLeague', 'league_id', 'status', 'ondate'));
	}
	public function ajaxMatchModal(Request $request){
		if( $request->ajax() ){
			$match_id = $request->match_id; 
			$schedule_id = $request->schedule_id ? $request->schedule_id : 0; 
			$ondate = $request->ondate ? $request->ondate : date('m/d/Y');
			$strDate = date('Ymd', strtotime($ondate));				
			
			
			$arrLeague = AccountLeague::where('account_id', self::$account_id)
				->leftJoin('league', 'league.league_id', '=', 'account_league.league_id')
				->where('league.provider', self::$provider)
				->select('league.league_id as league_id', 'league_name')
				->get();			

			$team_name = $request->input('team_name', '');

			$league_id = $request->input('league_id', 0);

			$status = $request->input('status',1);

			$team_name = trim($team_name);
				
			$queryChuaDa = Match::where([ 'status' => $status, 'provider' => self::$provider]);

			if($league_id > 0){
					$queryChuaDa->where('league_id', '=', $league_id);
				}	
			$chuaDaArr = $queryChuaDa->whereRaw("SUBSTRING(time_in_string,1,8) = '".$strDate."'")
			->where('user_id', self::$account_id)
			->where('ref_id', '<>', $match_id)
			->groupBy('ref_id')->get();

			if($team_name != ''){

				$queryChuaDa = Match::where([ 'status' => $status, 'provider' => self::$provider])->where('league_id', '=', $league_id);
					if($league_id > 0){
					$queryChuaDa->where('league_id', '=', $league_id);
				}	
				$queryChuaDa->where(function ($query) use ($team_name) {

			    		$query->where('team_name', '=', $team_name)		    		
			          	->orWhere('team_name2', '=', $team_name);

				});
				$queryChuaDa->where('user_id', self::$account_id);
				$queryChuaDa->where('ref_id', '<>', $match_id);
				$queryChuaDa->whereRaw('SUBSTRING(time_in_string,8) = '.$strDate);
				$chuaDaArr = $queryChuaDa->groupBy('ref_id')->get();
			}
			//dd($chuaDaArr);
			
			//dd(DB::getQueryLog());
			$arrSchedule = [];
			if($chuaDaArr->count()){
				foreach ($chuaDaArr as $match) {
					$ref_id = $match->ref_id;
					$arrSchedule[$ref_id] = ScheduleBet::where([ 'match_id' => $ref_id, 'provider' => self::$provider])->count();
				}
			}
		}
		return view('back.match.ajax-content-copy', compact('dangDaArr', 'chuaDaArr', 'team_name', 'arrSchedule', 'arrLeague', 'league_id', 'status', 'ondate', 'match_id', 'schedule_id'));
	}
	public function checkLeague($league_name){
	  $rs = true;
	  if($league_name == ''){
		$rs = false;
		return $rs;
	  }
	  $excludeArr = array('SPECIFIC 15 MINS', 'CORNERS', 'HOME TEAM/AWAY TEAM', 'OVER/UNDER', 'HALF', 'BOOKING', 'FREE KICK', 'GOAL KICK', 'Injury time awarded', 'OFFSIDE', 'OWN GOAL', 'PENALTY', 'RED CARD', 'GOAL', 'MINS', 'THROW IN', 'SUBSTITUTION', 'TOTAL GOALS', 'WHICH TEAM', 'EXTRA TIME', 'mins');
	  
	  
	   foreach ($excludeArr as $value) {      
		  if(strpos($league_name, $value) > 0){          
			  $rs = false;
			  break;
		  }		 
	   }
	   return $rs;
	}

	public function setProvider(Request $request){
		
		self::$provider = $request->provider ? $request->provider :  1;

		session(['provider' => self::$provider]);
		
		return redirect()->route('match.index');
	}

	public function addLeague(Request $request){
		$league_id = $request->league_id;

		AccountLeague::create(['league_id' => $league_id, 'account_id' => self::$account_id]);
		
		Session::flash('flash_message', 'Add league success!');

		return redirect()->route('league');
	}
	public function removeLeague(Request $request){
		$league_id = $request->league_id;

		AccountLeague::where(['league_id' => $league_id, 'account_id' => self::$account_id])->delete();
		
//		Match::where(['user_id' => self::$account_id, 'league_id' => $league_id ])->delete();		

		Session::flash('flash_message', 'Remove league success!');

		return redirect()->route('league');
	}
	public function setAccount(Request $request){
		
		$account_id = $request->account_id;

		session(['account_id' => $account_id]);

		return redirect()->route('match.index');
	}
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function bet($ref_id, Request $request)
	{	
		$account_id = self::$account_id;
		
		$bet_type = isset($request->bet_type) ? $request->bet_type : 1;

		$lichKeoArr = ScheduleBet::where([ 'match_id' => $ref_id, 'provider' => self::$provider, 'account_id' => $account_id])
			->whereIn('bet_type', array(1, 7))->orderBy('created_at', 'desc')->get();

		$lichOverArr = ScheduleBet::where([ 'match_id' => $ref_id, 'provider' => self::$provider, 'account_id' => $account_id])
			->whereIn('bet_type', array(3, 9))->orderBy('created_at', 'desc')->get();
		
		$matchDetail = Match::where(['ref_id' => $ref_id, 'provider' => self::$provider, 'user_id' => self::$account_id])->first();		
		
		$matchBetType1 = $matchBetType3 = $matchBetType7 = $matchBetType9 = array();
		
		$tmp = MatchBet::where([
			'ref_id' => $ref_id, 
			'provider' => self::$provider,
			'bet_type' => 1,
			'user_id' => self::$account_id
			])->orderBy('created_at', 'desc')
			->paginate(2);

		if($tmp->count()>0){
			foreach($tmp as $mb){
				$matchBetType1[] = $mb;
			}
		}
		$tmp = MatchBet::where([
			'ref_id' => $ref_id, 
			'provider' => self::$provider,
			'bet_type' => 3,
			'user_id' => self::$account_id
			])->orderBy('created_at', 'desc')
			->paginate(2);
		
		if($tmp->count()>0){
			foreach($tmp as $mb){
				$matchBetType3[] = $mb;
			}
		}
		$tmp = MatchBet::where([
			'ref_id' => $ref_id, 
			'provider' => self::$provider,
			'bet_type' => 7,
			'user_id' => self::$account_id
			])->orderBy('created_at', 'desc')
			->paginate(2);

		if($tmp->count()>0){
			foreach($tmp as $mb){
				$matchBetType7[] = $mb;
			}
		}	

		$tmp = MatchBet::where([
			'ref_id' => $ref_id, 
			'provider' => self::$provider,
			'bet_type' => 9,
			'user_id' => self::$account_id
			])->orderBy('created_at', 'desc')
			->paginate(2);

		if($tmp->count()>0){
			foreach($tmp as $mb){
				$matchBetType9[] = $mb;
			}
		}
		$arrUnderOver = ['0.25', '0.5', '0.75', '1', '1.25', '1.5', '1.75', '2', '2.25', '2.5', '2.75', '3', '3.25', '3.5', '3.75', '4', '4.25', '4.5', '4.75', '5'];
		$ratioArr = Ratio::where('provider', self::$provider)->orderBy('display_order', 'asc')->get();

		$priceArr = Ratio::where('provider', self::$provider)->orderBy('display_order', 'asc')->get();
		
		return view('back.match.bet')->with(compact('arrUnderOver', 'bet_type', 'priceArr', 'ratioArr', 'matchDetail', 'lichKeoArr', 'lichOverArr', 'matchBetType1', 'matchBetType3', 'account_id', 'matchBetType7', 'matchBetType9'));
	}

	public function ajaxLoadBet(Request $request)
	{		
		$ref_id = $request->ref_id;

		$account_id = self::$account_id;			

		$lichKeoArr = ScheduleBet::where([ 'match_id' => $ref_id, 'provider' => self::$provider, 'account_id' => $account_id])
			->whereIn('bet_type', array(1, 7))->orderBy('created_at', 'desc')->get();

		$lichOverArr = ScheduleBet::where([ 'match_id' => $ref_id, 'provider' => self::$provider, 'account_id' => $account_id])
		->whereIn('bet_type', array(3, 9))->orderBy('created_at', 'desc')->get();
		
		$matchDetail = Match::where(['ref_id' => $ref_id, 'provider' => self::$provider, 'user_id' => self::$account_id])->first();		
		
		$matchBetType1 = $matchBetType3 = $matchBetType7 = $matchBetType9 = array();
		
		$tmp = MatchBet::where([
			'ref_id' => $ref_id, 
			'provider' => self::$provider,
			'bet_type' => 1,
			'user_id' => self::$account_id
			])->orderBy('created_at', 'desc')
			->paginate(2);

		if($tmp->count()>0){
			foreach($tmp as $mb){
				$matchBetType1[] = $mb;
			}
		}
		$tmp = MatchBet::where([
			'ref_id' => $ref_id, 
			'provider' => self::$provider,
			'bet_type' => 3,
			'user_id' => self::$account_id
			])->orderBy('created_at', 'desc')
			->paginate(2);
		
		if($tmp->count()>0){
			foreach($tmp as $mb){
				$matchBetType3[] = $mb;
			}
		}	

		$tmp = MatchBet::where([
			'ref_id' => $ref_id, 
			'provider' => self::$provider,
			'bet_type' => 7,
			'user_id' => self::$account_id
			])->orderBy('created_at', 'desc')
			->paginate(2);

		if($tmp->count()>0){
			foreach($tmp as $mb){
				$matchBetType7[] = $mb;
			}
		}	

		$tmp = MatchBet::where([
			'ref_id' => $ref_id, 
			'provider' => self::$provider,
			'bet_type' => 9,
			'user_id' => self::$account_id
			])->orderBy('created_at', 'desc')
			->paginate(2);

		if($tmp->count()>0){
			foreach($tmp as $mb){
				$matchBetType9[] = $mb;
			}
		}		
		
		return view('back.match.ajax-load-bet')->with(compact('matchDetail', 'matchBetType1', 'matchBetType3', 'lichKeoArr', 'lichOverArr', 'matchBetType7', 'matchBetType9'));
	}

	public function storeBet(Request $request)
	{
	    $this->validate($request, [		   
		    'amount' => 'required|numeric|min:1',
		    'ratio_from' => 'numeric',
		    'ratio_to' => 'numeric',		    

		],[
			'amount.required' => 'Please input Bet amount.',
			'amount.numeric' => 'Bet amount is invalid.',			
			'ratio_from.numeric' => 'From Price is invalid.',
			'ratio_to.numeric' => 'To Price is invalid.',		
		]);	    


	    
	    $input = $request->all();
	    $score = trim($input['score']);
	    if($score != ''){
	    	$tmpScore = explode('-', $score);
	    	$input['score_1'] = trim($tmpScore[0]);
	    	$input['score_2'] = trim($tmpScore[1]);

	    }
		unset($input['score']);
	    if($input['ratio'] == ''){
	    	$input['ratio'] = null;
	    }
	    if(isset($input['exclude_price']) && $input['exclude_price'] == 1){
	    	$input['ratio_from'] = $input['ratio_to'] = null;
	    }
	    if(isset($input['exclude_time']) && $input['exclude_time'] == 1){
	    	$input['time_from'] = 1; 
	    	$input['time_to'] = 45;
	    }

	    $input['provider'] = self::$provider;
	    
	    $input['account_id'] = self::$account_id;
	    //var_dump($input);die;
	    ScheduleBet::create($input);

	    $match_id_copy = $request->match_id_copy;

	    if($match_id_copy != ''){
	    	$tmp = explode(',', $match_id_copy);
	    	foreach($tmp as $match_id){
	    		if( $match_id > 0 ){
	    			$input['match_id'] = $match_id;
	    			ScheduleBet::create($input);
	    		}
	    	}
	    }

	    Session::flash('flash_message', 'Add schedule bet success!');

	    return redirect()->back();
	}

	public function ajaxCopySchedule(Request $request){

		$schedule_id = $request->schedule_id;

		$detailArr = ScheduleBet::find( $schedule_id );


		$match_id_copy = $request->match_id_copy;

		if($match_id_copy != ''){

			$arrData['provider'] = $detailArr->provider;
			$arrData['account_id'] = $detailArr->account_id;			
			$arrData['bet_type'] = $detailArr->bet_type;
			$arrData['ratio'] = $detailArr->ratio;
			$arrData['priority'] = $detailArr->priority;
			$arrData['time_from'] = $detailArr->time_from;
			$arrData['time_to'] = $detailArr->time_to;
			$arrData['ratio_from'] = $detailArr->ratio_from;
			$arrData['ratio_to'] = $detailArr->ratio_to;
			$arrData['time_half'] = $detailArr->time_half;
			$arrData['amount'] = $detailArr->amount;
			$arrData['status'] = 1;

	    	$tmp = explode(',', $match_id_copy);

	    	foreach($tmp as $match_id){
	    		if( $match_id > 0 ){
	    			$arrData['match_id'] = $match_id;
	    			ScheduleBet::create($arrData);
	    		}
	    	}
	    }

	}

	public function destroyBet($ref_id, $bet_id, Request $request)
	{	
		ScheduleBet::destroy($bet_id);
		$lichArr = ScheduleBet::where('match_id', '=', $ref_id)->where('provider', self::$provider)->orderBy('created_at', 'desc')->get();

		$matchDetail = Match::where('ref_id', '=',$ref_id)->where('provider', self::$provider)->first();

		//var_dump($matchDetail);die;
		$ratioArr = Ratio::where('provider', self::$provider)->orderBy('display_order', 'asc')->get();
		Session::flash('flash_message', 'Delete schedule bet success!');
		return redirect('match/bet/'.$ref_id);
	}

}
