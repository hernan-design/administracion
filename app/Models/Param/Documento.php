<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;
use Cache;

class Documento extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'param.documento';

	protected $primaryKey = 'codigo_documento';

	public static function getClass($class) {
    	switch ($class) :
		    case 'person': return 1;
		    case 'entity': return 2;
			default: return 'Not class identified';
		endswitch;
    }

	public static function getTypeDocument($document) {
    	switch ($document) :
		    case 'rut': return 1;
		    case 'chamberCommerce': return 2;
		    case 'Brochure': return 3;
		    case 'other': return 4;
		    case 'image': return 5;
		    case 'document': return 6;
		    case 'summary': return 6;
		    case 'curriculum': return 10;
			case 'photocopy': return 11;
			default: return 'Not document identified';
		endswitch;
    }

	public static function getDocuments($class)
	{
		//$cache = sprintf('_%s_%s_%s', 'documents', $class, session('institution')->codigo_institucion);
		//if (Cache::has($cache)) {
		//	return Cache::get($cache);
		//}

		//return Cache::rememberForever($cache, function() use($class) {
			$query = Documento::query();
			$query->join('param.institucion_documento', 'documento.codigo_documento', '=', 'institucion_documento.codigo_documento');
			$query->where('codigo_institucion', session('institution')->codigo_institucion);
			$query->where('clasificacion_documento_institucion', Documento::getClass($class));
			$query->where('codigo_estado', 8);
			$query->orderby('nombre_documento','asc');
			return $query->get();
		//});
	}
}
