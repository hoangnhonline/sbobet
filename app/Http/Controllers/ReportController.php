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
use GearmanClient, Mail;

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
	public function pushGearman(Request $request){
		$data = $request->data;
		$job_name = $request->job_name;
		$gmclient= new \GearmanClient();			
		$gmclient->addServer("127.0.0.1", 4730);		
		$job_handle = $gmclient->doBackground($job_name, $data);
		if ($gmclient->returnCode() != GEARMAN_SUCCESS)
		{
		  echo "bad return code\n";
		  exit;
		}

		echo "done!\n";
	}
	public function updateRun(Request $request){
		$run = $request->run;
		$model = Account::find(self::$account_id);
		$model->run = $run;
		$model->save();
		$job_name = "match_update_status";
		$data = json_encode(['user_id' => self::$account_id]);
		//return view('back.report.mail-gearman', compact('job_name', 'data'));
		Mail::send('back.report.mail-gearman',
        [
            'job_name'          => $job_name,
            'data'             => $data
        ],
        function($message) use ($data) {
            $message->subject("Error gearman");
            $message->to('hoangnhonline@gmail.com');
            $message->from('hoangnhshopping@gmail.com', 'Auto Error');
            $message->sender('hoangnhshopping@gmail.com', 'Sbobet Bot');
   		});
   		die('123');
		if($run == 0){						
		
			try{
				
				/*
				$gmclient= new \GearmanClient();			
				$gmclient->addServer("127.0.0.1", 4730);		
				$job_handle = $gmclient->doBackground($job_name, $data);
				if ($gmclient->returnCode() != GEARMAN_SUCCESS)
				{
				  echo "bad return code\n";
				  exit;
				}

				echo "done!\n";
				*/

			}catch(\Exception $e){
				
			}
		}
		
		return redirect()->route('match.index');
	}	
	public function crawler(Request $request){
		$type = $request->type;
		$account_id = self::$account_id;
		$detailAccount = Account::find($account_id);
		$gmclient= new \GearmanClient();			
		$gmclient->addServer("127.0.0.1", 4730);		
		$job_handle = $gmclient->doBackground("crawler", json_encode(['user_id' => self::$account_id, 'type' => $type, 'user_name' => $detailAccount->username, 'user_alias' => $detailAccount->user_alias]));
		if ($gmclient->returnCode() != GEARMAN_SUCCESS)
		{
		  echo "bad return code\n";
		  exit;
		}

		echo "done!\n";
		
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
	public function detailLog(Request $request){
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
			$query->where('minute', '>=', $s->time_from);
			$query->where('minute', '<=', $s->time_to);
			$dataArr = $query->get();
		$account_id = self::$account_id;
		return view('back.report.detail-log', compact('dataArr', 's', 'haveLog', 'account_id'));
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
		$query->join('match', function($join)
		 {
		  $join->on('match.ref_id', '=', 'schedule_bet.match_id');
			$join->on('match.user_id', '=', 'schedule_bet.account_id');

		 });
		
		$dataArr = $query->whereRaw("DATE(schedule_bet.created_at) = '$ondate'")->where(['schedule_bet.provider' => self::$provider, 'schedule_bet.account_id' => self::$account_id])		
		->select('schedule_bet.*', 'team_name', 'team_name2')		
		->get();
		
		$account_id = self::$account_id;
		/*$thoaArr = [];
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
		*/
		//echo "<pre>";
		//var_dump($detailMatch);die;
		return view('back.report.schedule', compact('dataArr', 'ondate', 'status', 'bet_type', 'detailMatch', 'account_id'));
	}	
}
