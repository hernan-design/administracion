<?php

return [
    'field_change' => '<strong>:field</strong> cambió de :old_value a :new_value',

	'role' => [
		'updated' => 'Rol :title actualizado',
		'created' => 'Rol :title creado',

		'permission' => [
			'created' => 'Se ha asignado el permiso <strong>:display_name</strong> del módulo <strong>:nombre_modulo</strong> al rol :title',
			'deleted' => 'Se ha removido el permiso <strong>:display_name</strong> del módulo <strong>:nombre_modulo</strong> al rol :title'
		]
	],

	'user' => [
		'created' => 'Se ha creado el usuario <a class="blue" href="/usuario/:user_id">:user</a>',
		'role' => [
			'created' => 'Se ha asignado el rol <strong>:title</strong> a <a class="blue" href="/usuario/:user_id">:user</a>',
			'deleted' => 'Se ha removido el rol <strong>:title</strong> a <a class="blue" href="/usuario/:user_id">:user</a>'
		],
		'speciality' => [
		'created' => 'Se ha asignado la especialidad <strong>:title</strong> a <a class="blue" href="/usuario/:user_id">:user</a>',
		'deleted' => 'Se ha removido la especialidad <strong>:title</strong> a <a class="blue" href="/usuario/:user_id">:user</a>'
		],
		'sector' => [
		'created' => 'Se ha asignado el sector económico <strong>:title</strong> a <a class="blue" href="/usuario/:user_id">:user</a>',
		'deleted' => 'Se ha removido el sector económico <strong>:title</strong> a <a class="blue" href="/usuario/:user_id">:user</a>'
		],
		'session' => [
			'platform' => '<a class="blue" href="/usuario/:user_id">:user</a> ingresó a la plataforma'
		],
    	'modifications' => 'Se editaron los siguiente campos <strong> :modification </strong>',
		'log' => 'Se ha <strong>:message</strong> al mentor <strong>:mentor</strong>'
	],

	'mentor' => [
		'sync' => 'El usuario :user - :user_id ha sincronizado el indice de algolia al mentor <strong>:mentor</strong>',
		'desync' => 'El usuario :user - :user_id ha eliminado el indice de algolia  al mentor <strong>:mentor</strong>'
	],

	'mentoring' => [
		'created' => 'Se ha creado la asesoría <strong>No.</strong> <a class="blue" href="/mentoria/:mentoring">:mentoring</a> , se asigno el mentor <b> :user_asign </b> al proyecto <b> :entity </b>',
		'deleted' => 'Se ha eliminado la asesoría <strong>No.</strong> <a class="blue" href="/mentoria/:mentoring">:mentoring</a>, del mentor <b> :user_asign </b> al proyecto <b> :entity </b>',
		'received' => 'Se te asigno como mentor en la asesoría <strong> No. </strong> <a class="blue" href="/mentoria/:mentoring">:mentoring</a>, al proyecto <b> :entity </b> por <a class="blue" href="/usuario/:mentor_code">:user_mentor</a>'
	],

	'event' => [
		'inscription' => '<a class="blue" href="/usuario/:user_id">:user</a> se ha inscrito al evento <strong>:title</strong>',
		'invitation' => 'Se ha invitado al usuario <a class="blue" href="/usuario/:user_id">:user</a> al evento <strong>:title</strong>',
		'invitation_user' => 'Se te ha invitado al evento <strong>:title</strong>',
		'uninvitation' => 'Se ha cancelado la invitacion al usuario <a class="blue" href="/usuario/:user_id">:user</a> al evento <strong>:title</strong>',
		'uninvitation_user' => 'Se te ha cancelado la invitacion al evento <strong>:title</strong>',
		'sync' => 'El usuario :user - :user_id ha sincronizad el indice de algolia  al evento <strong>:evento</strong>',
		'desync' => 'El usuario :user - :user_id ha eliminado el indice de algolia  al evento <strong>:evento</strong>',
		'unsuscribe' => '<a class="blue" href="/usuario/:user_id">:user</a> ha cancelado la inscripción al evento <strong>:title</strong>',
		'attended' => 'Se ha confirmado la asistencia de <a class="blue" href="/usuario/:user_id">:user</a> al evento <strong>:title</strong>',
		'attended_user' => 'Se ha confirmado tu asistencia al evento <strong>:title</strong>',
		'removed' => 'Se ha eliminado la confirmación de asistencia de <a class="blue" href="/usuario/:user_id">:user</a> al evento <strong>:title</strong>',
		'activated' => 'Se ha activado el evento <strong>:event</strong>',
		'deactivated' => 'Se ha desactivado el evento <strong>:event</strong>',
	],

	'product' => [
		'sync' => 'El usuario :user ha activado el producto <strong>:producto</strong>',
		'desync' => 'El usuario :user ha inactivado el producto <strong>:producto</strong>',
	],

	'promotion' => [
		'approve' => 'Se ha aprobado <strong>:title</strong> No. <a class="blue" href="/marketplace/promociones/:promotion_id">:promotion_id</a>',
		'back' => 'Se ha rechazado <strong>:title</strong> No. <a class="blue" href="/marketplace/promociones/:promotion_id">:promotion_id</a>',
		'activate' => 'Se ha activado <strong>:title</strong> No. <a class="blue" href="/marketplace/promociones/:promotion_id">:promotion_id</a>',
		'inactive' => 'Se ha desactivado <strong>:title</strong> No. <a class="blue" href="/marketplace/promociones/:promotion_id">:promotion_id</a>',
	],

	'reports' => 'Se ha generado el reporte <strong>:report</strong>',

  'program' => 'Se ha modificado el resultado en la convocatoria <strong> :convocatoria </strong>',

  'voucher' => 'Se ha cancelado la transaccion <strong> :purchase </strong>',

  'call' => [
	'inscription' => 'Se ha inscrito la entidad <a class="blue" href="/usuario/:user_id">:user</a> a la convocatoria <strong>:title</strong>',
	'sync' => 'El usuario :user - :user_id ha sincronizado el indice de algolia a la convocatoria <strong>:convocatoria</strong>',
	'desync' => 'El usuario :user - :user_id ha eliminado el indice de algolia  a la convocatoria <strong>:convocatoria</strong>',
	'activated' => 'La convocatoria <strong>:convocatoria</strong> fue activada',
	'deactivated' => 'La convocatoria <strong>:convocatoria</strong> fue desactivada',
  ],

  'curse' => [
    'inscription' => '<a class="blue" href="/usuario/:user_id">:user</a> se ha inscrito al curso <strong>:title</strong>',
    'unsuscribe' => '<a class="blue" href="/usuario/:user_id">:user</a> ha cancelado la inscripción al curso <strong>:title</strong>',
  ],
  'course' => [
      'activated' => 'Se ha activado el curso <strong>:course</strong>',
      'deactivated' => 'Se ha desactivado el curso <strong>:course</strong>',
  ],

  'assign' => [
    'evaluator' => 'Se asigno el evaluador <a class="blue" href="/usuario/:user_id">:user</a> al proyecto <strong>:proyect</strong>',
    'evaluator_automatic' => 'Se te asigno como evaluador a la convocatoria <strong> :convocatoria </strong>',
    'delete_evaluator_automatic' => 'Se te eliminó como evaluador a la convocatoria <strong> :convocatoria </strong>',
    'unsucribe_evaluator' => 'Se elimino la asignacion del evaluador <a class="blue" href="/usuario/:user_id">:user</a> al proyecto <strong>:proyect</strong>',
	'evaluated' => 'Se evaluó el proyecto <strong>:project</strong>',
	'received' => 'Se te asigno como evaluador al proyecto <strong> :proyect </strong> por <a class="blue" href="/usuario/:user_id_responsable">:user_responsable</a>',
	'unreceived' => 'Se te elimino como evaluador al proyecto <strong> :proyect </strong> por <a class="blue" href="/usuario/:user_id_responsable">:user_responsable</a>',
	'notification' => 'Se ha enviado notificacion sobre la evaluación para la etapa de perfilamiento
	 <b>:name_email</b> al proyecto <b>:project</b>'
  ],
  'certificate' => [
	  'notification' =>  'Se genero el certificado del evento <b> :event_name </b> para el usuario <a class="blue" href="/usuario/:user_id"> :user </a> con el codigo de certificado <b> :code_certificate </b>',
	  'notification_user' =>  'Se te genero el certificado del evento <b> :event_name </b> con el codigo de certificado <b> :code_certificate </b>' 
  ],
  'industry' => [
	  'activated' => 'Se ha activado la Industria <strong>:industry</strong>',
	  'deactivated' => 'Se ha desactivado la Industria <strong>:industry</strong>',
  ],
  'etapa' => [
		'activated' => 'Se ha activado la Etapa <strong>:etapa</strong>',
		'deactivated' => 'Se ha desactivado la Etapa <strong>:etapa</strong>',
	],
	'archives' => [
		'add'		=> 'Se agregaron archivos al proyecto <strong>:project</strong>',
		'delete' 	=> 'Se han eliminado archivos al proyecto <strong>:project</strong>'
	],
	'attendance' => [
		'add'		=> 'Se agregó archivo de asistencia al evento <strong>:event</strong>',
		'delete' 	=> 'Se ha eliminado un archivo de asistencia del evento <strong>:event</strong>'
	],
	'cargaexcel' => [
		'usuarios'		=> [
			'add'		=> 'Se agregó archivo para Carga de Usuarios',
			'delete' 	=> 'Se ha eliminado archivo para Carga de Usuarios',
		],
		// 'delete' 	=> 'Se ha eliminado un archivo de asistencia del evento <strong>:event</strong>'
	],
];
