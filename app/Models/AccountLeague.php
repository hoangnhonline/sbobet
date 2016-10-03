<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Presenters\DatePresenter;

class AccountLeague extends Model  {

	use DatePresenter;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $timestamps = false;
	
	protected $table = 'account_league';
	
	protected $fillable = [
        'account_id',
        'league_id'
    ];

}