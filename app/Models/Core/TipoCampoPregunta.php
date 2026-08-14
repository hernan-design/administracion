<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class TipoCampoPregunta extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.tipo_campo_pregunta';

   	protected $primaryKey = 'codigo_tipo_campo_pregunta';

	public $timestamps = false;


	public static function getTypeQuestion($type) {
		switch ($type):
			case 'simple_text': return 1;
			case 'long_text': return 2;
			case 'number': return 3;
			case 'year': return 4;
			case 'municipality': return 5;
			case 'password': return 6;
			case 'terms_conditions': return 7;
			case 'email': return 8;
			case 'url_strict': return 9;
			case 'simple_url': return 10;
			case 'image': return 11;
			case 'image_multiple': return 12;
			case 'archive': return 13;
			case 'open_date': return 14;
			case 'select': return 15;
			case 'ciiu': return 16;
			case 'level_study': return 17;
			case 'economic_sector': return 18;
			case 'money': return 19;
			case 'radios': return 20;
			case 'checkbox': return 21;
			case 'phone': return 22;
			case 'percentage': return 23;
			case 'integer': return 24;
			case 'identification_document': return 25;
			case 'previous_date': return 26;
			case 'negative_money': return 27;
			default: return 'Not type identified';
		endswitch;
	}
}
