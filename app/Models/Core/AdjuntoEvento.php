<?php

namespace  App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Config, App, AWS, Validator, Input;
use TuProyecto, Auth,  DB, Log;
use App\Models\Seguridad\Usuario,
    App\Models\Core\TipoAdjunto,
    App\Models\Core\PrivacidadContenido,
    App\Models\Core\Evento;

class AdjuntoEvento extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'core.adjunto_evento';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
                            'codigo_evento',
                            'codigo_documento'
                        ];

    /*public function isValid($data)
    {
        $rules = array(
            'codigo_evento' => 'required',
            'codigo_documento' => 'required'
        );

        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            return true;
        }
        $this->errors = $validator->errors();
        return false;
    }*/

    public static function getAttachedUser( $tipo_adjunto = null)
    {
        // Decrypt src-params
        if( is_numeric(Input::get('codigo_evento'))){
            $codigo_evento = Input::get('codigo_evento');
        }else{
            list($codigo_evento) = TuProyecto::decryptSrcParams(Input::get('codigo_evento'));
        }

        if(!$codigo_evento || !is_numeric($codigo_evento)) {
            App::abort(403);
        }

        $evento = Evento::find($codigo_evento);
        if(!$evento instanceof Evento) {
            App::abort(404);
        }


        $attachedUser = [];
        $query = AdjuntoEvento::query();
        $query->select(
            'adjunto_evento.codigo_adjunto',
            'codigo_tipo_adjunto',
            'direccion_adjunto',
            'nombre_adjunto',
            'adjunto_evento.codigo_evento',
            'privacidad_contenido.codigo_privacidad_contenido',
            'privacidad_contenido.nombre_privacidad_contenido',
            'documento.codigo_documento',
            'documento.nombre_documento'
            //'usuario.codigo_usuario',
            //DB::raw('(CASE WHEN entidad.codigo_usuario IS NOT NULL THEN true ELSE false END) AS entity')
        );
        $query->join('core.adjunto', 'adjunto_evento.codigo_adjunto', '=', 'adjunto.codigo_adjunto');
        $query->join('param.documento', 'adjunto_evento.codigo_documento', '=', 'documento.codigo_documento');
        $query->join('core.privacidad_contenido', 'adjunto_evento.codigo_privacidad_contenido', '=', 'privacidad_contenido.codigo_privacidad_contenido');
        $query->join('core.evento', 'adjunto_evento.codigo_evento', '=', 'evento.codigo_evento');
       // $query->leftJoin('core.entidad', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
        $query->where('adjunto_evento.codigo_estado', '=', Estado::getState('active'));
        $query->where('adjunto_evento.codigo_evento', '=', $evento->codigo_evento);

        if(!is_null($tipo_adjunto)) {
            $query->where('codigo_tipo_adjunto', '=', $tipo_adjunto);
        }else{
            $query->where('codigo_tipo_adjunto', '!=', TipoAdjunto::getTypeAttached('gallery'));
        }

        $query->orderBy('adjunto_evento.codigo_evento','desc');
        // Get posts
        $data = $query->get();

        if(count($data) > 0) {
            foreach ($data as $attached) {
            $attachedUser[] = AdjuntoEvento::buildAttached($attached);
            }
        }
        return $attachedUser;
    }

    /**
     * Build Attached User.
     */
    public static function buildAttached($attached)
    {
        $object = new \stdClass();
        $object->uuid = TuProyecto::encryptSrcParams([$attached->codigo_adjunto]);
        $object->codigo_adjunto = TuProyecto::encryptSrcParams([$attached->codigo_adjunto]);
        $object->codigo_tipo_adjunto = $attached->codigo_tipo_adjunto;
        $object->codigo_privacidad_contenido = $attached->codigo_privacidad_contenido;
        $object->label_privacidad_contenido = $attached->nombre_privacidad_contenido;
        $object->codigo_documento = $attached->codigo_documento;
        $object->label_documento = $attached->nombre_documento;
        $object->direccion_adjunto = $attached->direccion_adjunto;
        $object->name = $attached->nombre_adjunto;

        $object->owner = Auth::check() && Auth::user()->codigo_usuario == $attached->codigo_usuario ? true : false;
        $object->owner = true;
        return $object;
    }

    public static function getDataAttached($attached, $evento)
    {
        $query = AdjuntoEvento::query();
        $query->select(
                'adjunto_evento.codigo_adjunto',
                'codigo_tipo_adjunto',
                'direccion_adjunto',
                'nombre_adjunto',
                'adjunto_evento.codigo_evento',
                'privacidad_contenido.codigo_privacidad_contenido',
                'privacidad_contenido.nombre_privacidad_contenido',
                'documento.codigo_documento', 'documento.nombre_documento'
            );
        $query->join('core.adjunto', 'adjunto_evento.codigo_adjunto', '=', 'adjunto.codigo_adjunto');
        $query->join('param.documento', 'adjunto_evento.codigo_documento', '=', 'documento.codigo_documento');
        $query->join('core.privacidad_contenido', 'adjunto_evento.codigo_privacidad_contenido', '=', 'privacidad_contenido.codigo_privacidad_contenido');
        $query->join('core.evento', 'adjunto_evento.codigo_evento', '=', 'evento.codigo_evento');
        //$query->leftJoin('core.entidad', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
        $query->where('adjunto_evento.codigo_estado', '=', Estado::getState('active'));
        $query->where('adjunto_evento.codigo_evento', '=', $evento);
        $query->where('adjunto.codigo_adjunto', '=', $attached);
        return $query->first();
    }

/*
    public function entidad()
    {
        return $this->hasOne('App\Entidad','codigo_usuario','codigo_usuario');
    }
    */

}
