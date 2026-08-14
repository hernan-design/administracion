<?php

namespace App\Models\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class Notificacion extends Model
{
    protected $connection = 'mongodb';
   	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $collection = 'notifications';

    protected $dates = ['created'];

    public static function getType($type) {
    	switch ($type) :
		    case 'general': return 1;
		    case 'friendship': return 2;
		    case 'like': return 3;
		    case 'favorite': return 4;
		    case 'discussion': return 5;
		endswitch;
    }
}
