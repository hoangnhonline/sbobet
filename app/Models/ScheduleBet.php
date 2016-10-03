<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Presenters\DatePresenter;

class ScheduleBet extends Model  {

	use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */

	protected $table = 'schedule_bet';
	
	public $timestamps = true;

	protected $fillable = [
        'provider',
        'match_id',
        'ratio',
        'priority',
		'bet_type',
        'time_from',
        'time_to',
        'amount',
        'time_half',
		'ratio_from',
		'ratio_to',
		'account_id'
    ];

}