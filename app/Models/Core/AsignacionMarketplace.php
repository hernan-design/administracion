<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use DB, Auth, Input, App, Lang, Validator, URL, Log;

use App\Models\Seguridad\Usuario;

class AsignacionMarketplace extends Model
{
	/**
	* The database table used by the model.
	*
	* @var string
	*/
	protected $table = 'core.asignacion_marketplace';

    protected $primaryKey = 'codigo_asignacion_mp';

    public $timestamps = false;

	protected $fillable = ['codigo_registro_fase_usuario', 'codigo_usuario'];

	/**
	* Valid create assignment marketplace.
	* @param $data stdClass
	* @return Boolean
	*/
   	public function isValid($data)
    {
        $rules = [
			'codigo_registro_fase_usuario' => 'required',
			'codigo_usuario' => 'required'
		];

     	$validator = Validator::make($data, $rules);
        if ($validator->passes()) {
     		return true;
        }
        $this->errors = $validator->errors();
        return false;
    }

	public static function userExists($codigo_usuario, $codigo_registro_fase_usuario)
	{
		$query = AsignacionMarketplace::query();
		$query->where('codigo_usuario', $codigo_usuario)
			  ->where('codigo_registro_fase_usuario', $codigo_registro_fase_usuario)
			  ->where('codigo_estado', Estado::getState('active'));

		return $query->first();
	}

	public static function getDataMember($assignment)
	{
		$query = AsignacionMarketplace::query();

		$query->select('codigo_asignacion_mp', 'codigo_registro_fase_usuario', 'fecha_asignacion_mp', 'estado.codigo_estado', 'estado.nombre_estado', 'usuario.correo_electronico',
			DB::raw("(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) as nombre_usuario"));

		$query->join('core.estado', 'asignacion_marketplace.codigo_estado', '=', 'estado.codigo_estado');
		$query->join('seguridad.usuario', 'asignacion_marketplace.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');

		// conditions for user and states
		$query->where('asignacion_marketplace.codigo_registro_fase_usuario', $assignment->codigo_registro_fase_usuario);
        $query->where('asignacion_marketplace.codigo_usuario', $assignment->codigo_usuario);
		$query->whereIn('asignacion_marketplace.codigo_estado', [Estado::getState('active')]);

		return $query->first();
	}

	public static function getAssignmentsMember($codigo_registro_fase)
	{
		$query = AsignacionMarketplace::query();
		$query->select('codigo_asignacion_mp', 'codigo_registro_fase_usuario', 'fecha_asignacion_mp', 'estado.codigo_estado', 'estado.nombre_estado', 'usuario.correo_electronico', DB::raw("(usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) as nombre_usuario"));
		$query->join('core.estado', 'asignacion_marketplace.codigo_estado', '=', 'estado.codigo_estado');
		$query->join('seguridad.usuario', 'asignacion_marketplace.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
		$query->whereIn('asignacion_marketplace.codigo_estado', [Estado::getState('active')]);
		$query->where('asignacion_marketplace.codigo_registro_fase_usuario', $codigo_registro_fase);
		return $query->get();
	}

	public static function getAssignmentMembers(array $filters = [])
	{
		$query = Usuario::query();
		$query->select(DB::raw('distinct usuario.codigo_usuario as id'),
			DB::raw("initcap( (usuario.nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ) ) as text")
		);
		$query->leftJoin('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
		$query->join('core.asignacion_marketplace', 'usuario.codigo_usuario', '=', 'asignacion_marketplace.codigo_usuario');

		if( isset($filters['q']) && !empty($filters['q']) ) {
            $query->whereRaw("(COALESCE(trim(usuario.nombre_usuario), '') || ' ' || COALESCE(trim(persona.primer_apellido_persona), '') || ' ' || COALESCE(trim(persona.segundo_apellido_persona), '')) ilike '%".trim($filters['q'])."%'");
        }

		$query->whereIn('asignacion_marketplace.codigo_estado', [Estado::getState('active')]);
		$query->orderBy('text', 'asc');
		return $query->get();
	}

	/**
    * Build Assignment.
	* @param App\Models\Core\AsignacionMarketplace $assignment
	* @param App\Models\Seguridad\Usuario $persona
	* @return $object stdClass
    */
    public static function buildAssignment($assignment)
    {
        $object = new \stdClass();
        $object->nombre_usuario = $assignment->nombre_usuario;
		$object->correo_electronico = $assignment->correo_electronico;
		$object->codigo_estado = $assignment->codigo_estado;
		$object->nombre_estado = $assignment->nombre_estado;
		$object->codigo_asignacion_mp = $assignment->codigo_asignacion_mp;
		$object->codigo_registro_fase_usuario = $assignment->codigo_registro_fase_usuario;
		$object->fecha_asignacion_mp = $assignment->fecha_asignacion_mp;
        return $object;
    }
}
