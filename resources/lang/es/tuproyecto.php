<?php

return [
	// Entidades
	'entities' => [
		'project' => [
			'stage' => 'Etapa',
			'industry' => 'Industria'
		],
        'report' => [
            'microsector' => 'Microsector',
            'macrosector' => 'Macrosector',
            'tiposociedad'=> 'Tipo sociedad',
            'cubrimiento' => 'Cubrimiento'
        ]
	],

	'fields' => [
		'rol' => [
			'title' => 'Nombre',
			'description' => 'Descripción'
		]
	],

	'exception' => 'Ha ocurrido un error inesperado por favor recarga tu navegador.',


  // Mentorias
  'mentoring' => [
	'subjectFinishedMentoring' => 'El mentor :nameMentor ha finalizado una sesión de mentoría',
	'subjectApplicationProcess' => 'Solicitud de proceso de acompañamiento',
    'invitation' => 'Aún no eres mentor. <a href=":route" class="button xtiny sky-blue radius btn-shadow btn-lowerc btn-clean-margin">Conviértete en mentor</a> y obtén beneficios apoyando a emprendedores a lograr sus metas.',
    'description' => 'Este espacio esta diseñado para centralizar el proceso de acompañamiento que a partir de este momento desarrollaran mentor y mentorizado, recuerden que todos los documento o información que requieran deberá enviarse por este canal en la opción de adjuntos.',
    'create' => [
      'experience' => 'Mi experiencia',
      'select' => 'Elige un proyecto',
      'specialties' => 'Especialidades (áreas de experiencia)',
      'highlightedspecialty' => 'Especialidad Destacada',
      'sectors' => 'Sectores Económicos (industrias)',
      'attached' => 'Adjunto Hoja de vida',
      'terms1' => 'Acepto los',
	  'terms2' => 'de <span>mentoría de la plataforma.</span>',
	],
	'process' => [
		'request' => 'Nos alegra informarte que te hemos asignado la empresa :entity, ya puedes iniciar el proceso de acompañamiento con el(a) empresario(a) :requesting.'
		// 'Nos alegra informarte que el Programa de Emprendimiento Compensar te ha asignado la empresa :entity, ya puedes iniciar el proceso de acompañamiento con el(a) empresario(a) :requesting. <br><br> Cualquier inquietud, por favor comunicarse con Maribel Carrión al correo mcarriong@compensar.com'
	],
		'processMassive' => [
			'request' => 'Nos alegra informarte que te hemos asignado las siguientes empresas, ya puedes iniciar el proceso de acompañamiento con los(a) respectivos empresarios(a):',
			'processAccept' => 'Cordialmente te informamos que tu solicitud realizada al mentor :requesting ha sido aceptada para iniciar el proceso de acompañamiento.',
			'mentorconfirm_subject' => 'Se ha programado una sesión de mentoría con :user .',
		],
		'event' => [
			'description' => 'Espacio disponible sesión de mentoría.'
		],
		'sessionMassive' => [
			'request' => 'Se ha creado la sesión masiva, puedes descargar el siguiente excel para el desarrollo de cada una.',
		]

    ],

	'dashboard' => [
		'events' => [
			'course' => 'En curso',
			'expired' => 'Vencidos'
		],
		'programs' => [
			'open' => 'Abiertas',
			'closed' => 'Cerradas'
		],
		'articles' => [
			'approved' => 'Aprobados',
			'rejected' => 'No aprobados'
		],
		'accompaniments' => [
			'minusthree' => 'Menos de tres días',
			'three' => 'Tres días',
			'six' => 'Seis días'
		],
		'discussions' => [
			'articles' => 'Artículos',
			'courses' => 'Cursos',
			'forums' => 'Foros',
			'programas' => 'Convocatorias'
		]
	],
	// Evaliations
	'evaluation' => [
		'tabs' => [
			'provider' => 'Proveedor',
			'evaluation' => 'Evaluación',
			'entreprenaur' => 'Comprador',
      'bill' => 'Facturación'
		],
    'evaluation_recom' => '¿Recomienda los servicios prestados por el asesor a otro empresario?',
    'evaluation_apobe' => '¿Apruebas el pago al asesor sobre el servicio prestado?'
	],

	'text' => [
		'calendar' => 'Hemos creado un nuevo evento para ti, esperamos puedas asistir.',
		'blog' => 'Queremos que estés informado, no te pierdas nuestra última actualización del blog.',
		'course' => 'Desarrolla nuevas habilidades, participa en nuestro módulo aprende e interactúa con nuestro nuevo contenido.',
		'program' => 'Tenemos disponible una nueva convocatoria para ti ¡No te quedes sin participar, conócela!'
	],

	'text_security' => [
		'email' => ''
	],

  'programs' => [
    'results' => 'Resultados'
  ],

  'marketplace' => [
    'exception_assign' => 'Falta asignar un asesor a la transacción'
  ],
  //Correos
  'emails' => [
	'voucher' => 'Asignación de voucher',
  ]
];
