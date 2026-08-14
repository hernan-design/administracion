<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Default Filesystem Disk
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default filesystem disk that should be used
	| by the framework. A "local" driver, as well as a variety of cloud
	| based drivers are available for your choosing. Just store away!
	|
	| Supported: "local", "ftp", "s3", "rackspace"
	|
	*/

	'default' => 's3',

	/*
	|--------------------------------------------------------------------------
	| Default Cloud Filesystem Disk
	|--------------------------------------------------------------------------
	|
	| Many applications store files both locally and in the cloud. For this
	| reason, you may specify a default "cloud" driver here. This driver
	| will be bound as the Cloud disk implementation in the container.
	|
	*/

	'cloud' => 's3',

	/*
	|--------------------------------------------------------------------------
	| Filesystem Disks
	|--------------------------------------------------------------------------
	|
	| Here you may configure as many filesystem "disks" as you wish, and you
	| may even configure multiple disks of the same driver. Defaults have
	| been setup for each driver as an example of the required options.
	|
	*/

	'disks' => [

		'local' => [
			'driver' => 'local',
			'root'   => storage_path('app'),
		],

		'ftp' => [
			'driver'   => 'ftp',
			'host'     => 'ftps.compensar.co',
			'username' => 'SVCFTPTUPROYECTO',
			'password' => 'Pr0y3ct0.2019',
      		'port'     => 1015,
			// Optional FTP Settings...
			// 'port'     => 21,
			// 'root'     => '',
			// 'passive'  => true,
			// 'ssl'      => true,
      		// 'timeout'  => 30,
		],

		'sftp' => [
			'driver'   => 'sftp',
			'host'     => 'ftps.compensar.co',
			'username' => 'SVCFTPTUPROYECTO',
			'password' => 'SVCFTPTUPROYECTO',

			// Optional FTP Settings...
			// 'port'     => 21,
			// 'root'     => '',
			// 'passive'  => true,
			// 'ssl'      => true,
			// 'timeout'  => 30,
		],

		's3' => [
			'driver' => 's3',
			'key'    => env('AWS_BUCKET_KEY','AKIAY4SNKXXZPEMCUJNS'),
			'secret' => env('AWS_BUCKET_SECRET', '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'),
			'region' => env('AWS_REGION', 'us-west-2'),
			'bucket' => env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com')
			// 'url' => 'drp6skpdlhxna.cloudfront.net',
		],

		'rackspace' => [
			'driver'    => 'rackspace',
			'username'  => 'your-username',
			'key'       => 'your-key',
			'container' => 'your-container',
			'endpoint'  => 'https://identity.api.rackspacecloud.com/v2.0/',
			'region'    => 'IAD',
			'url_type'  => 'publicURL',
		],

	],

];
