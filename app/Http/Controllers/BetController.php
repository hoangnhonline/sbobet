<?php namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use App\Models\Match;
use App\Models\Ratio;
use App\Models\ScheduleBet;

class BetController extends Controller {

	/**
	 * The CommentRepository instance.
	 *
	 * @var App\Repositories\CommentRepository
	 */
	protected $schedule_bet;

	/**
	 * Create a new CommentController instance.
	 *
	 * @param  App\Repositories\CommentRepository $comment_gestion
	 * @return void
	 */
	public function __construct(
		ScheduleBet $schedule_bet)
	{
		$this->schedule_bet = $schedule_bet;

		$this->middleware('admin', ['except' => ['store', 'edit', 'update', 'destroy']]);
		$this->middleware('auth', ['only' => ['store', 'update', 'destroy']]);
		$this->middleware('ajax', ['only' => ['updateSeen', 'update', 'valid']]);
	}

	

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Illuminate\Http\Request $request
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(
		Request $request, 
		$id, $match_id)
	{
		$this->schedule_bet->destroy($id);

		if($request->ajax())
		{
			return response()->json(['id' => $id]);
		}

		return redirect('match/');
	}

	

}
