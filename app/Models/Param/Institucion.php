<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;
use Request, DB, Config;
use App\Models\Core\Estado,
	App\Models\Param\Parametro,
	App\Models\Param\VersionTerminos,
	App\Models\Param\TerminosCondiciones,
	App\Models\Seguridad\Modulo;

class Institucion extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'param.institucion';

	protected $primaryKey = 'codigo_institucion';

	public $timestamps = false;

    public static function institution($institution) {
    	switch ($institution) :
			case 'andi': return 17;
			case 'unisabana': return 20;
			case 'redcoo': return 29;
			case 'compensar': return 30;
			case 'terpel': return 37;
			case 'bolivar': return 38;
			case 'cenisoft': return 46;
			case 'demo': return 48;
			case 'innpulsa': return 51;
			case 'ecopetrol': return 53;
			case 'creame': return 64;
			case 'medellinstartupcity': return 66;
			case 'dinamiza': return 69;
			case 'sacudete': return 70;
			case 'serviu': return 74;
			case 'saroa': return 57;
			case 'propace': return 75;
			case 'hilandoempresa': return 23;
			case 'bwee': return 73;
			case 'colabora': return 79;
			case 'tureto': return 77;
			case 'hogarescomunidad': return 58;
			case 'muy': return 47;
			case 'ninojesus': return 59;
			case 'atrevetemipyme': return 60;
			case 'rutan': return 61;
			case 'siinova': return 62;
			case 'betsalem': return 77;
			case 'metamorfosis': return 70;
      case 'prana': return 80;
			case 'copo': return 68;
			default: return 'Not institution identified';
		endswitch;
    }

	public static function getNameInstitution(int $institution): string
	{
    	switch ($institution) :
			case 17: return 'andi';
			case 20: return 'unisabana';
			case 29: return 'redcoo';
			case 30: return 'compensar';
			case 37: return 'terpel';
			case 38: return 'bolivar';
			case 46: return 'cenisoft';
			case 48: return 'demo';
			case 51: return 'innpulsa';
			case 53: return 'ecopetrol';
			case 64: return 'creame';
			case 66: return 'medellinstartupcity';
			case 69: return 'dinamiza';
			case 70: return 'sacudete';
			case 74: return 'serviu';
			case 57: return 'saroa';
			case 75: return 'propace';
			case 23: return 'hilandoempresa';
			case 73: return 'bwee';
			case 79: return 'colabora';
			case 77: return 'tureto';
			case 58: return 'hogarescomunidad';
			case 47: return 'muy';
			case 59: return 'ninojesus';
			case 60: return 'atrevetemipyme';
			case 61: return 'rutan';
			case 62: return 'siinova';
      case 77: return 'betsalem';
      case 70: return 'metamorfosis';
			case 68: return 'copo';
      case 80: return 'prana';
			default: return 'not_institution';
		endswitch;
    }

    public static function getInstitution()
    {
   		$institution = self::institution('compensar');
		// if (strpos(Request::getHost(), 'terpel') !== falses) {
		// 	$institution = self::institution('terpel');
  		// }elseif (strpos(Request::getHost(), 'bolivar') !== false) {
		//	$institution = self::institution('bolivar');
		// }
		if (strpos(Request::getHost(), 'colabora') !== false) {
			$institution = self::institution('colabora');
		}elseif (strpos(Request::getHost(), 'hilandoempresa') !== false) {
			$institution = self::institution('hilandoempresa');
		}elseif (strpos(Request::getHost(), 'innpulsa') !== false) {
			$institution = self::institution('innpulsa');
		}elseif (strpos(Request::getHost(), 'medellinstartupcity') !== false) {
			$institution = self::institution('medellinstartupcity');
		}elseif (strpos(Request::getHost(), 'redcoo') !== false) {
			$institution = self::institution('redcoo');
		}elseif (strpos(Request::getHost(), 'sabana') !== false) {
			$institution = self::institution('unisabana');
		}elseif (strpos(Request::getHost(), 'demo') !== false) {
			$institution = self::institution('demo');
		}elseif (strpos(Request::getHost(), 'andi') !== false) {
			$institution = self::institution('andi');
		}elseif (strpos(Request::getHost(), 'emprendimientometropol') !== false) {
			$institution = self::institution('creame');
		}elseif (strpos(Request::getHost(), 'dinamiza') !== false) {
			$institution = self::institution('dinamiza');
		}elseif (strpos(Request::getHost(), 'sacudete') !== false) {
			$institution = self::institution('sacudete');
		}elseif (strpos(Request::getHost(), 'comunidademprendepais') !== false) {
			$institution = self::institution('bolivar');
		}elseif (strpos(Request::getHost(), 'lms') !== false) {
			$institution = self::institution('muy');
		}elseif (strpos(Request::getHost(), 'compensar') !== false) {
			$institution = self::institution('compensar');
		}elseif (strpos(Request::getHost(), 'cenisoft') !== false) {
			$institution = self::institution('cenisoft');
		}elseif (strpos(Request::getHost(), 'serviu') !== false) {
			$institution = self::institution('serviu');
		}elseif (strpos(Request::getHost(), 'saroa') !== false) {
			$institution = self::institution('saroa');
		}elseif (strpos(Request::getHost(), 'propace') !== false) {
			$institution = self::institution('propace');
		}elseif (strpos(Request::getHost(), 'bwee') !== false) {
			$institution = self::institution('bwee');
		}elseif (strpos(Request::getHost(), 'tureto') !== false) {
			$institution = self::institution('tureto');
		}elseif (strpos(Request::getHost(), 'hogarescomunidad') !== false) {
			$institution = self::institution('hogarescomunidad');
		}elseif (strpos(Request::getHost(), 'ninojesus') !== false) {
			$institution = self::institution('ninojesus');
		}elseif (strpos(Request::getHost(), 'atrevetemipyme') !== false) {
			$institution = self::institution('atrevetemipyme');
		}elseif (strpos(Request::getHost(), 'rutan') !== false) {
			$institution = self::institution('rutan');
		}elseif (strpos(Request::getHost(), 'mipymeinnovabolivar') !== false) {
			$institution = self::institution('siinova');
		}elseif (strpos(Request::getHost(), 'betsalem') !== false) {
			$institution = self::institution('betsalem');
		}elseif (strpos(Request::getHost(), 'metamorfosis') !== false) {
			$institution = self::institution('metamorfosis');
		}elseif (strpos(Request::getHost(), 'prana') !== false) {
			$institution = self::institution('prana');
		}elseif (strpos(Request::getHost(), 'copo') !== false) {
			$institution = self::institution('copo');
    }
		$institution = self::findOrFail($institution);
		return $institution;
	}

	public static function getTypeCamp($camp) {
		switch ($camp) {
			case 'homepage':
				return 1;
			break;

			case 'plataforma':
				return 2;
			break;

			default:
				return null;
			break;
		}
	}

	public static function getInstitutions(array $filters = []) {

		$institution = Institucion::query();
		$institution->select('institucion.codigo_institucion', 'institucion.nombre_institucion', 'institucion.codigo_estado');

		if (!empty($filters['institution_name'] && isset($filters['institution_name']))) {
			$institution->where('institucion.nombre_institucion', 'like', '%'.$filters['institution_name'].'%');
		}

		if (!empty($filters['institution_state'] && isset($filters['institution_state']))) {
			$institution->where('institucion.codigo_estado', '=', $filters['institution_state']);
		}

		$institution->whereIn('institucion.codigo_estado', [Estado::getState('active'), Estado::getState('inactive')]);
		$institution->orderBy('institucion.codigo_estado', 'ASC');
		$data = $institution->get();

		return $data;
	}

	public function metaTags()
	{
		return (Object) DB::table('param.meta')
			->leftjoin('param.meta_institucion', function($join) {
				$join->on('param.meta.codigo_meta', '=', 'param.meta_institucion.codigo_meta')
					->where('param.meta_institucion.codigo_institucion', '=', $this->codigo_institucion)
					->where('param.meta_institucion.codigo_estado', '=', Estado::getState('active'));
	        })
	     	->select('meta_institucion.valor_meta_institucion', 'meta.nombre_meta')
	   		->pluck('valor_meta_institucion', 'nombre_meta')->toArray();
	}

	public function color()
	{
		return DB::table('param.institucion')
			->leftjoin('param.parametro_institucion', function($join) {
	            $join->on('param.institucion.codigo_institucion', '=', 'param.parametro_institucion.codigo_institucion')
	        		->where('param.parametro_institucion.codigo_parametro', '=', Parametro::getParameter('background_body'))
					->where('param.parametro_institucion.codigo_estado', '=', Estado::getState('active'));
	        })
			->select(DB::raw("COALESCE(valor_parametro, '".Config::get('tuproyecto.color')."') AS color_institucion"))
			->where('param.parametro_institucion.codigo_institucion', '=', $this->codigo_institucion)
		   	->value('color_institucion');
	}

	public static function getHomeInstitution($institution) {
		$home = '';
		switch ($institution) {
            case 'muro':
                $home = 'Comunidad';
            break;
            case 'cursos':
                $home = 'Cursos';
            break;
            case 'programas':
                $home = 'Convocatoria';
            break;
		}

		return $home;
	}

	public static function generalParametersInstitution($type, $institution) {
		$query = Parametro::query();
		$query->select('parametro.codigo_parametro', 'parametro.codigo_tipo_parametro', 'parametro.nombre_parametro', 'parametro.descripcion_parametro', 'parametro.codigo_clase_parametro',
		'parametro.codigo_tipo_campo', 'parametro.codigo_estado', 'parametro_modulo.codigo_modulo', 'parametro_modulo.valor_parametro_modulo');
		$query->leftJoin('param.parametro_modulo', function($join) use($institution) {
			$join->on('parametro_modulo.codigo_parametro', '=', 'parametro.codigo_parametro')
				->where('parametro_modulo.codigo_institucion', '=', $institution);
		});
		$query->leftJoin('seguridad.modulo', 'modulo.codigo_modulo', '=', 'parametro_modulo.codigo_modulo');
		$query->where('parametro.codigo_tipo_campo', '=', self::getTypeCamp($type));
		$query->where('parametro.codigo_estado', '=', Estado::getState('active'));
		$query->orderBy('codigo_tipo_parametro', 'ASC');

		return $query->get();
	}

	public static function buildGeneralDatesInstitution($institution, $parameters, $typeParametersPlataform) {
		$object = new \stdClass();
		$object->contacto_institucion = $institution->contacto_institucion;
		$object->texto_registro_institucion = $institution->texto_registro_institucion;
		$object->contenido_registro_institucion = $institution->contenido_registro_institucion;
		$object->recomendaciones_marketplace = $institution->recomendaciones_marketplace;
		$object->parametros = $parameters;
		$object->parametrosPlataforma = $typeParametersPlataform;
		$object->valorParametros = ParametroModulo::getValueParameters($institution->codigo_institucion);
		$object->meta = Meta::all();
		$object->modulo = Modulo::getModulesParametrized();
		$object->terminos_condiciones = isset(VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('terms'))->texto_version_terminos) ? VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('terms'))->texto_version_terminos : '';
		$object->politicas_privacidad = isset(VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('privacy'))->texto_version_terminos) ? VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('privacy'))->texto_version_terminos : '';
		$object->politica_ventas = isset(VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('sales'))->texto_version_terminos) ? VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('sales'))->texto_version_terminos : '';
		$object->politicas_mentoria = isset(VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('mentoring'))->texto_version_terminos) ? VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('mentoring'))->texto_version_terminos : '';
		$object->politicas_evaluadores = isset(VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('evaluator'))->texto_version_terminos) ? VersionTerminos::getValidationTerms($institution->codigo_institucion, TerminosCondiciones::getTypeTerms('evaluator'))->texto_version_terminos : '';

		return $object;
	}
}
