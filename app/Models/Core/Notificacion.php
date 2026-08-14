<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use DB, Log, Validator, SendEmail;

use App\Models\Core\Estado, App\Models\Seguridad\Usuario, App\Models\Core\Notificacion, App\Models\Core\DetalleNotificacionUsuario, App\Models\Core\DetalleNotificacionCanal, App\Models\Core\CanalUsuario, App\Models\Param\Institucion;

use App\Events\Notification;

use App\Models\Mongodb\Notificacion as MDBNotificacion;
use App\Models\Mongodb\Usuario as MDBUsuario;

use MongoDB\BSON\ObjectID;

class Notificacion extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.notificacion';

	protected $primaryKey = 'codigo_notificacion';

	public $timestamps = false;

	protected $fillable = ['codigo_modulo', 'texto_notificacion', 'tipo_notificacion', 'codigo_recurso_modulo', 'destinatarios_notificacion'];

	/**
	 * Type
	 *
	 * @var array
	 */
	public static $type = [1 => 'Módulo', 2  => 'Texto'];
	
	/**
	 * Addressee
	 *
	 * @var array
	 */
	public static $addressee = [1 => 'Dirigido a', 2  => 'Global'];

	public static function addressee()
	{
		$array = self::$addressee;

		switch (session('institution')->codigo_institucion) {
			case Institucion::institution('dinamiza'):
				array_push($array, 'Filtros');
			break;
		}

		return $array;
	}

	public function isValid($data)
	{
		$rules = [
			'texto_notificacion' => 'required|max:180',
			'destinatarios_notificacion' => 'required'
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	/**
	 * Get type notification
	 */
	public static function getTypeNotification($type)
	{
		switch ($type) {
			case 'module': return 1;
		    case 'text': return 2;
			default: return 'Not type notification identified';
		}
	}

	/**
	 * Get addressee notification
	 */
	public static function getAddresseeNotification($addressee)
	{
		switch ($addressee) {
			case 'addressee_to': return 1;
			case 'global': return 2;
			case 'filters': return 3;
			default: return 'Not addressee notification identified';
		}
	}

	public static function notifications(array $filters = [])
	{
		$query = Notificacion::query();
		$query->select('codigo_notificacion', 'titulo_notificacion', 'texto_notificacion', 'fecha_notificacion', 'codigo_estado');

		if(isset($filters['tipo_notificacion']) && !empty($filters['tipo_notificacion'])){
			$query->where('tipo_notificacion', $filters['tipo_notificacion']);
		}

		if(isset($filters['fecha_notificacion']) && !empty($filters['fecha_notificacion'])){
			list($dateNotiIni, $dateNotiFin) = explode(' - ', $filters['fecha_notificacion']);
			
			$query->whereBetween(DB::raw("(fecha_notificacion)::timestamp"), [$dateNotiIni, $dateNotiFin]);
		}

		$query->where('codigo_institucion', session('institution')->codigo_institucion);
		$query->where('codigo_estado', Estado::getState('active'));
		$query->orderBy('codigo_notificacion', 'desc');
		return $query;
	}

	public function notifyUsers($job, $data)
	{
		try {
			$users = [];
			$is_global = false;
			
			if($data['destinatarios_notificacion'] == Notificacion::getAddresseeNotification('addressee_to')){
				if(isset($data['channels']) && !empty($data['channels'])) {    					
					if( is_array($data['channels']) ){
						foreach($data['channels'] as $channel){
							$notificationChannel = new DetalleNotificacionCanal;
							$notificationChannel->codigo_canal = $channel;
							$notificationChannel->codigo_estado = Estado::getState('active');
							$notificationChannel->codigo_notificacion = $data['codigo_notificacion'];
							$notificationChannel->save();
						}
					}else {
						$notificationChannel = new DetalleNotificacionCanal;
						$notificationChannel->codigo_canal = $data['channels'];
						$notificationChannel->codigo_estado = Estado::getState('active');
						$notificationChannel->codigo_notificacion = $data['codigo_notificacion'];
						$notificationChannel->save();
					}
					
					$users = CanalUsuario::usersChannelNotification( $data );
				}
				
				if(isset($data['users']) && !empty($data['users'])){
					foreach($data['users'] as $user){
						$notificationDetail = new DetalleNotificacionUsuario;
						$notificationDetail->codigo_usuario = $user;
						$notificationDetail->codigo_estado = Estado::getState('active');
						$notificationDetail->codigo_notificacion = $data['codigo_notificacion'];
						$notificationDetail->save();
					}
				}
				
				$users = collect([$users, $data['users']])->collapse();
			}

			if($data['destinatarios_notificacion'] == Notificacion::getAddresseeNotification('global')){
				$is_global = true;
			}

			if($data['destinatarios_notificacion'] == Notificacion::getAddresseeNotification('filters')){
				if(isset($data['update']) && $data['update']){
					$users = $data['users'];
				}else{
					$query = Usuario::query();
					$query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
					$query->leftJoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
					$query->leftJoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');
	
					//  User institution
					$query->join('core.usuario_institucion', function($join) use($data){
						$join->on('usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
						->where('usuario_institucion.codigo_institucion', '=', $data['codigo_institucion'])
						->whereNotIn('usuario_institucion.codigo_estado', [Estado::getState('inactive'), Estado::getState('removed')]);
					});
					
					if( isset($data['codigo_convocatoria']) && !empty($data['codigo_convocatoria']) ) {
						$query->join('core.registro_fase_usuario', 'usuario.codigo_usuario', '=', 'registro_fase_usuario.codigo_usuario');
						$query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');
						$query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
						$query->where('convocatoria.codigo_convocatoria', '=', $data['codigo_convocatoria']);
					}
	
					if( isset($data['fecha_nacimiento_usuario']) && !empty($data['fecha_nacimiento_usuario']) ) {
						$query->where("usuario.fecha_nacimiento_usuario", '=', $data['fecha_nacimiento_usuario']);
					}
					
					if( isset($data['codigo_nivel_educacion']) && !empty($data['codigo_nivel_educacion']) ) {
						$query->where("persona.codigo_nivel_educacion", '=', $data['codigo_nivel_educacion']);
					}
					
					if( isset($data['codigo_lista_ocupacion']) && !empty($data['codigo_lista_ocupacion']) ) {
						$query->where("persona.codigo_lista_ocupacion", '=', $data['codigo_lista_ocupacion']);
					}
	
					if( isset($data['codigo_ciudad']) && !empty($data['codigo_ciudad']) ) {
						$query->where("usuario.codigo_ciudad", '=', $data['codigo_ciudad']);
					}
					
					if( isset($data['codigo_localidad_ciudad']) && !empty($data['codigo_localidad_ciudad']) ) {
						$query->where("usuario.codigo_localidad_ciudad", '=', $data['codigo_localidad_ciudad']);
					}
	
					if( isset($data['codigo_barrio_localidad']) && !empty($data['codigo_barrio_localidad']) ) {
						$query->where("usuario.codigo_barrio_localidad", '=', $data['codigo_barrio_localidad']);
					}
					
					if( isset($data['codigo_estrato']) && !empty($data['codigo_estrato']) ) {
						$query->where("persona.codigo_estrato", '=', $data['codigo_estrato']);
					}
					
					if( isset($data['codigo_tipo_vivienda']) && !empty($data['codigo_tipo_vivienda']) ) {
						$query->where("persona.codigo_tipo_vivienda", '=', $data['codigo_tipo_vivienda']);
					}
					
					if( isset($data['jefe_hogar']) && !empty($data['jefe_hogar']) ) {
						$query->where("persona.jefe_hogar", '=', $data['jefe_hogar']);
					}
	
					if( isset($data['codigo_sector_trabajo']) && !empty($data['codigo_sector_trabajo']) ) {
						$query->where("persona.codigo_sector_trabajo", '=', $data['codigo_sector_trabajo']);
					}
	
					if( isset($data['enterprise']) && !empty($data['enterprise']) ) {
						if($data['enterprise'] == 'true'){
							$query->whereIn('usuario.codigo_usuario', function($query)
							{
								$query->select(DB::raw('distinct "usuario"."codigo_usuario"
								from "core"."entidad"
								inner join "seguridad"."usuario" on "entidad"."seg_codigo_usuario" = "usuario"."codigo_usuario"'));
							});
						}
	
						if($data['enterprise'] == 'false'){
							$query->whereNotIn('usuario.codigo_usuario', function($query)
							{
								$query->select(DB::raw('distinct "usuario"."codigo_usuario"
								from "core"."entidad"
								inner join "seguridad"."usuario" on "entidad"."seg_codigo_usuario" = "usuario"."codigo_usuario"'));
							});
						}
					}
					
					if( isset($data['codigo_autodenominacion']) && !empty($data['codigo_autodenominacion']) ) {
						$query->where("persona.codigo_autodenominacion", '=', $data['codigo_autodenominacion']);
					}
					
					if( isset($data['codigo_grupo_poblacional']) && !empty($data['codigo_grupo_poblacional']) ) {
						$query->where("persona.codigo_grupo_poblacional", '=', $data['codigo_grupo_poblacional']);
					}
					
					if( isset($data['codigo_grupo_comunitario']) && !empty($data['codigo_grupo_comunitario']) ) {
						$query->where("persona.codigo_grupo_comunitario", '=', $data['codigo_grupo_comunitario']);
					}
					
					if( isset($data['codigo_grupo_social']) && !empty($data['codigo_grupo_social']) ) {
						$query->where("persona.codigo_grupo_social", '=', $data['codigo_grupo_social']);
					}
					
					if( isset($data['disability']) && !empty($data['disability']) ) {
						$query->where("persona.discapacidad", '=', $data['disability']);
					}
	
					$query->where('usuario.tipo_usuario', '=',  Usuario::typeUser('person'));
	
					$users = $query->pluck('usuario.codigo_usuario')->toArray();
				}

				foreach($users as $user){
					$notificationDetail = new DetalleNotificacionUsuario;
					$notificationDetail->codigo_usuario = $user;
					$notificationDetail->codigo_estado = Estado::getState('active');
					$notificationDetail->codigo_notificacion = $data['codigo_notificacion'];
					$notificationDetail->save();
				}
			}

			if($is_global){
				$userNotify = Usuario::join('core.usuario_institucion', 'usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
					->where('usuario_institucion.codigo_institucion', $data['codigo_institucion'])->get();
			}else{
				$userNotify = Usuario::whereIn('codigo_usuario', $users)->get();
			}

			if( (isset($data['envio_plataforma']) && !empty($data['envio_plataforma'])) || $is_global ){
				foreach ($userNotify as $user) {
					$receivedBy = MDBUsuario::where('cod', $user->codigo_usuario)->first();
					$deliveredBy = MDBUsuario::where('cod', $data['codigo_usuario'])->first();

	                if($receivedBy instanceof MDBUsuario && $deliveredBy instanceof MDBUsuario) {
						$notification = new MDBNotificacion;
						$notification->receivedBy = new ObjectID($receivedBy->_id);
						$notification->deliveredBy = new ObjectID($deliveredBy->_id);
						$notification->title = $data['title'];
						$notification->content = $data['content'];
						$notification->url = $data['url'];
						$notification->type = $data['type'];
						$notification->module = $data['module'];
						$notification->resource = $data['resource'];
						$notification->isGlobal = $is_global;
						$notification->image = $data['logo_institucion'];
						$notification->institution = $data['codigo_institucion'];
						$notification->created = date('Y-m-d H:i:s');
						$notification->save();
					}
				}
			}

			if( (isset($data['envio_email']) && !empty($data['envio_email'])) || $is_global ){
				foreach ($userNotify as $user) {
					if(!empty($user->correo_electronico)){
						$dataMail = [];
						$dataMail['_template'] = 'emails.notification';
						$dataMail['_subject'] = "Notificación {$data['nombre_institucion']}";
						$dataMail['_to'] = $user->correo_electronico;
						$dataMail['_to_name'] = ucwords(strtolower($user->nombre_usuario));
						$dataMail['_inst'] = $data['codigo_institucion'];
						$dataMail['_url'] = $data['url'];
						$dataMail['_content'] = $data['content'];
						
						// Send Mail Notification
						SendEmail::notification($dataMail);
					}
				}
			}	
			
		} catch (\Exception $e) {
			Log::error(sprintf('%s:%s', 'Notificacion:notifyUsers', $e->getMessage()));
        } finally {
        	// Delete Job
			$job->delete();
		}
	}

	/**
	 * User notification
	 */
	public function usersNotification()
	{
		return $this->hasMany('App\Models\Core\DetalleNotificacionUsuario', 'codigo_notificacion', 'codigo_notificacion');
	}

	/**
	 * Channel notification
	 */
	public function channelsNotification()
	{
		return $this->hasMany('App\Models\Core\DetalleNotificacionCanal', 'codigo_notificacion', 'codigo_notificacion');
	}
}
