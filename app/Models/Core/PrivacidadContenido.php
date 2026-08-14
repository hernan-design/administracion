<?php

namespace App\Models\Core;

use Input;

use Illuminate\Database\Eloquent\Model;
use Cache;
class PrivacidadContenido extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.privacidad_contenido';

   	protected $primaryKey = 'codigo_privacidad_contenido';

    public static function getPrivacy($privacy)
    {
        switch ($privacy) :
            case 'private': return 1;
            case 'public': return 2;
            case 'hidden': return 3;
            default: return 'Not privacy identified';
        endswitch;
    }

	public static function getContentPrivacy(Array $denied = [], Array $allowed = [], $filter = null, $lang = null)
	{
		$query = PrivacidadContenido::query();
		$query->select('codigo_privacidad_contenido as id', 'nombre_privacidad_contenido as text');
		if(count($denied) > 0) {
			$query->whereNotIn('codigo_privacidad_contenido', $denied);
		}

		if(count($allowed) > 0) {
			$query->whereIn('codigo_privacidad_contenido', $allowed);
		}

		if($filter) {
			$query->whereRaw("nombre_privacidad_contenido ilike '%".$filter."%'");
		}
		$data = $query->get();

		// Helptext
		$lang = $lang ?: 'app.privacy';
		foreach ($data as $privacy) {
			// Title
			$key_title = "$lang.$privacy->id";
			$title = trans($key_title);
			$title = $key_title != $title ? $title : trans("app.privacy.$privacy->id");

			// Help
			$key_help = "$lang.help.$privacy->id";
			$help = trans($key_help, ['plataforma' => session('institution')->nombre_institucion]);
			$help = $key_help != $help ? $help : trans("app.privacy.help.$privacy->id");

			$privacy->text = sprintf('%s (%s)', $title, $help);
		}
		return $data;
	}

	public static function getPrivacyContent()
    {
        $cache = sprintf('_%s', 'privacy_content');
        if (Cache::has($cache)) {
            return Cache::get($cache);
        }

        return Cache::rememberForever($cache, function() {
            $query = PrivacidadContenido::query();
            $query->orderby('codigo_privacidad_contenido','asc');
            return $query->get();
        });
    }
}
