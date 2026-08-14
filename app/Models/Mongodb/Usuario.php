<?php

namespace App\Models\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class Usuario extends Model
{
    protected $connection = 'mongodb';
   	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $collection = 'users';
}
