<?php namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Models\BetResult;
use App\Models\Account;
use App\Models\Match;
use App\Models\ScheduleBet;
use App\Models\MatchBetDetail;
use Session;
use DB;
use GearmanClient;

class ReportController extends Controller {

	public static $provider ;
	public static $account_id ;

	public function __construct()
	{	
			
		$this->middleware('admin');
		
		self::$provider = session('provider')!= NULL ? session('provider') : 2;
		self::$account_id = session('account_id');
		$detailAccount = Account::find(self::$account_id);
		$accountArr = Account::where('provider', self::$provider)->where('status', 1)->get();		

		view()->share([ 'accountArr' => $accountArr, 'provider' => self::$provider, 'detailAccount' => $detailAccount]);


	}
public function updateRun(Request $request){
		$run = $request->run;
		$model = Account::find(self::$account_id);
		$model->run = $run;
		$model->save();
		if($run == 0){			
			$gmclient= new \GearmanClient();			
			$gmclient->addServer("127.0.0.1", 4730);		
			$job_handle = $gmclient->doBackground("match_update_status", json_encode(['user_id' => self::$account_id]));
			if ($gmclient->returnCode() != GEARMAN_SUCCESS)
			{
			  echo "bad return code\n";
			  exit;
			}

			echo "done!\n";
		}
		return redirect()->route('match.index');
	}	
	public function statement(Request $request)
	{	
		$ondate = $request->ondate ? $request->ondate : date('m/d/Y');	
		
		$dataArr = BetResult::where(['bet_date' => $ondate, 'provider' => self::$provider, 'user_id' => self::$account_id])->get();
		
		return view('back.report.statement', compact('dataArr', 'ondate'));
	}	
	public function viewLog(Request $request){
		$schedule_id = $request->schedule_id;
		$s = ScheduleBet::find($schedule_id);	
		$haveLog = MatchBetDetail::where([
			'half' => $s->time_half,
			'user_id' => self::$account_id,
			'ref_id' => $s->match_id])->count();
			
		$query = MatchBetDetail::where([
			'half' => $s->time_half,
			'user_id' => self::$account_id,
			'ref_id' => $s->match_id,
			'bet_type' => $s->bet_type			
			]);			
			if($s->bet_type == 3 || $s->bet_type == 9){
				$query->whereRaw("(bet_ratio - (score + score2)) = $s->ratio");
			}else{
				$query->where('bet_ratio', '=', $s->ratio);
			}
			$query->where('minute', '>=', $s->time_from);
			$query->where('minute', '<=', $s->time_to);
			$dataArr = $query->get();
		$account_id = self::$account_id;
		return view('back.report.view-log', compact('dataArr', 's', 'haveLog', 'account_id'));
	}
	public function reportSchedule(Request $request)
	{			
		$detailMatch = [];
		$ondate = $request->ondate ? $request->ondate : date('Y-m-d');
		$status = $request->status ? $request->status : null;
		$bet_type = $request->bet_type ? $request->bet_type : null;
		$query = ScheduleBet::whereRaw('1');
		if( $bet_type ){
			$query->where('bet_type', $bet_type);
		}
		if( $status){
			if( $status == 1 || $status == 2){
				$query->where('schedule_bet.status', $status);	
			}else{
				$query->where('schedule_bet.status','>', 2);	
			}
		}
		$dataArr = $query->whereRaw("DATE(schedule_bet.created_at) = '$ondate'")->where(['schedule_bet.provider' => self::$provider, 'schedule_bet.account_id' => self::$account_id])		
		->select('schedule_bet.*')		
		->get();
		$str_match_id = '';
		foreach( $dataArr as $data){
			$match_id = $data->match_id;
			$str_match_id .= $match_id.",";
			$tmp  = Match::where('ref_id', $match_id)->where('user_id', self::$account_id)->first();
			if($tmp){
				$detailMatch[$match_id] = $tmp->toArray();
			} 
		}
		$account_id = self::$account_id;
		$thoaArr = [];
		$thoa = $khongthoa = $success = 0;
		if( $dataArr->count() > 0){
			foreach($dataArr as $data){
				if($data->status == 2){
					$success++;
				}
				$s = ScheduleBet::where('id', $data->id)->first();
				//var_dump("<pre>", $s);die;
				$query = MatchBetDetail::where([
				'half' => $s->time_half,
				'user_id' => $account_id,
				'ref_id' => $s->match_id,
				'bet_type' => $s->bet_type			
				]);			
				if($s->bet_type == 3 || $s->bet_type == 9){
					$query->whereRaw("(bet_ratio - (score + score2)) = $s->ratio");
				}else{
					$query->where('bet_ratio', '=', $s->ratio);
				}
				$query->where('minute', '>=', $s->time_from);
				$query->where('minute', '<=', $s->time_to);
				$dArr = $query->get();
				$countThoa = 0;
				if($dArr->count() > 0){
					
					foreach($dArr as $d){
						if( $s->priority == 'h' && $d->bet_ratio2 >= $s->ratio_from && $d->bet_ratio2 <= $s->ratio_to ){
							$countThoa++;	
						}
						if($s->priority == 'a' && $d->bet_ratio3 >= $s->ratio_from && $d->bet_ratio3 <= $s->ratio_to){
							$countThoa++;
						}
					}
				}
				$thoaArr[$data->id] = $countThoa;
				if( $countThoa > 0){
					$thoa++;
				}else{
					$khongthoa++;
				}
			}
		}
		
		//echo "<pre>";
		//var_dump($detailMatch);die;
		return view('back.report.schedule', compact('dataArr', 'ondate', 'status', 'bet_type', 'detailMatch', 'account_id', 'thoaArr', 'thoa', 'khongthoa', 'success'));
	}	
}
