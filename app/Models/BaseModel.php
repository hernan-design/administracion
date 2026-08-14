<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	protected $nullable = [];

	/**
	* Listen for save event
	*/
	public static function boot()
	{
		parent::boot();
		static::saving(function($model) {
			self::setNullables($model);
		});
	}

	/**
	* Set empty nullable fields to null
	* @param object $model
	*/
	protected static function setNullables($model)
	{
		foreach($model->nullable as $field) {
			if(empty($model->{$field}))
			{
				$model->{$field} = null;
			}
		}
	}
}
