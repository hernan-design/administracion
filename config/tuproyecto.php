<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Global
	|--------------------------------------------------------------------------
	|
	| Default global config values.
	|
	*/

	'key' => '1',

	'title' => 'TuProyecto',

	'privacy' => false,

	'noreply' => 'info@tuproyecto.com',

	'secure_domains' => ['http://redemprendedoresbavaria.net', 'http://tuproyecto.com', 'http://andidelfuturo.com', 'http://emprendedoreslafayette.com', 'http://inversionangel.com', 'http://seissabana.com'],

	'src_params_separate' => '[.tp.]',

	/*
	|--------------------------------------------------------------------------
	| Meta Tags
	|--------------------------------------------------------------------------
	|
	| Default values.
	|
	*/

	'header_logo' => 'https://lh3.googleusercontent.com/-LhFQNEyTy3M/VI90FGjAIMI/AAAAAAAAL6k/EiZKL3ywp34/w30-h27-no/TuProyecto_Barra.png',

	'logo' => 'https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/institutions/logo-amarillo-blanco-100x19.png',

	'color' => '#000000',

	'url' => 'http://tuproyecto.com',

	'favicon' => 'https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/institutions/favicon.ico.png',

	'description' => 'Construye TuProyecto. Recibe aportes y opiniones de mentores para llevarlo a una etapa de financiación. Es TuProyecto pero no estas solo.',

	'keywords' => 'emprendimiento,conectividad,innovacion,proyecto,asesoria en proyecto,ayuda en proyecto,financiacion de proyectos,financiacion colectiva,crowdfunding,crowdfunding colombia,apoyo a proyectos',

	'social_image' => 'https://lh5.googleusercontent.com/-cD_824Lu_N4/VKLNUZHJl3I/AAAAAAAAMsE/fVKso0Ngu4c/s611-no/tuproyecto_devs.jpeg',

	// 'avatar_image' => 'http://static.tuproyecto.com/images/svg/no-user-image.svg',
	'avatar_image' => 'https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/general/plataforma-no-profile-image.png',

	'no_image' => 'https://s3-us-west-2.amazonaws.com/static.tuproyecto.com/images/svg/no-image.svg',

	/*
	|--------------------------------------------------------------------------
	| Key APIs Default
	|--------------------------------------------------------------------------
	|
	| Default API keys
	|
	*/
	'grecatcha_api_key' => '6LdCY0sUAAAAAHPUA9Yz0uvDTa0Vg9mZKgzmN-uj',

	'grecatcha_secret_key' => '6LdCY0sUAAAAAPC3DD4c5lo7izux5mtIXLgRDqHQ',

	/*
	** AWS
	*/
	'url_download_aws' => 'https://s3-us-west-2.amazonaws.com/static.tuproyecto.com',


	/*
    |--------------------------------------------------------------------------
    | Sync Algolia Auto On Update or Create Item
    |--------------------------------------------------------------------------
    |
    | This is  a custom parameter for tuproyecto.com
    | the parameter is 'true' to allow  sync to algolia 
	| when running php artisan algolia:sync
    */

    'algolia_sync' => env('ALGOLIA_SYNC', true) 

);
