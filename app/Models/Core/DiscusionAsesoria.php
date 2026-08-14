<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use TuProyecto, Input, DB;

use App\Models\Seguridad\Usuario, App\Models\Core\Discusion;

class DiscusionAsesoria extends Model
{
	/**
	* The database table used by the model.
	*
	* @var string
	*/
	protected $table = 'core.discucion_sesoria';

	public $incrementing = false;

	public $timestamps = false;

	public static function getDiscussionMentor()
	{
		$discussions = [];
		$query = Discusion::query();
		$query->select('discucion.codigo_discucion', 'discucion.texto_discucion', 'discucion.codigo_usuario', 'usuario.nickname_usuario', 'usuario.imagen_usuario', DB::raw("(CASE usuario.tipo_usuario WHEN ".Usuario::typeUser('person')." THEN nombre_usuario || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '') ELSE nombre_usuario END) as nombre_usuario"), 'discucion.fecha_discucion','discucion.contador_comentarios_discucion','discucion.contador_like');

		$query->join('core.discusion_asesoria', 'discucion.codigo_discucion', '=', 'discusion_asesoria.codigo_discucion');
		// User Discussion
		$query->join('seguridad.usuario', 'discucion.codigo_usuario', '=', 'usuario.codigo_usuario');
		$query->leftJoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');
		$query->where('discusion_asesoria.codigo_asesoria', '=', Input::get('codigo_asesoria'));
		$query->where('discucion.codigo_estado', Estado::getState('active'));

   		$query->orderBy('discucion.codigo_discucion','desc');
		// Get total posts
		$total_entries = $query->get()->count();

		// Get posts
		$data = $query->skip(Input::get('offset'))->take(Input::get('limit'))->get();

		if(count($data) > 0) {
			foreach ($data as $discussion) {
				$discussions[] = DiscusionAsesoria::buildDiscussion($discussion);
			}
		}

		$response = new \stdClass();
		$response->total_entries = $total_entries;
		$response->items = $discussions;
   		return $response;
	}

	/**
	* Build Discussion.
	*/
   	public static function buildDiscussion($post)
   	{
		$object = new \stdClass();
		$object->codigo_discucion = TuProyecto::encryptSrcParams([$post->codigo_discucion]);
		$object->codigo_usuario = $post->codigo_usuario;
		$object->texto_discucion = $post->texto_discucion;

		$object->nombre_usuario = $post->nombre_usuario;
		$object->imagen_usuario = $post->imagen_usuario;
		$object->nickname_usuario = $post->nickname_usuario;
		$object->fecha_discusion = $post->fecha_discucion;

		$object->contador_comentarios_discucion = $post->contador_comentarios_discucion;
		$object->contador_like = $post->contador_like;
		return $object;
   	}
}
