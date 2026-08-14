<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class VerPregEncuesta extends Model
{
	protected $table = 'core.ver_preg_encuesta';

	public $timestamps = false;

	public $incrementing = false;

	public static function surveyQuestionClass($class)
	{
		switch ($class) {
			case 'question': return 1;
			case 'subquestion': return 2;
			default: return 'Not class identified';
		}
	}
}
