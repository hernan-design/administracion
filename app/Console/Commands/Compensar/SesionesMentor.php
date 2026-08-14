<?php

namespace App\Console\Commands\Compensar;

use Illuminate\Console\Command;

use Log, Lang, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\RolUsuario,
App\Models\Aprende\Mentoria;

class SesionesMentor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compensar:sesionmentor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $codigo_rol = 7;
        $codigo_institucion = 30;
        $fecha_inicial = '2021-08-01';
        $fecha_final = '2021-08-31';
        $correo = 'wilsonemartinez@gmail.com';

        $mentores_institucion = RolUsuario::where('role_id','=',$codigo_rol)
                                ->where('rol_usuario.codigo_institucion','=',$codigo_institucion)
                                ->where('mentor.correo_electronico','=',$correo)
                                ->where('fecha_mentoria','>=',$fecha_inicial)
                                ->where('fecha_mentoria','<=',$fecha_final)
                                ->join('seguridad.usuario as mentor','rol_usuario.user_id','=','mentor.codigo_usuario')
                                ->join('aprende.mentoria','mentoria.seg_codigo_usuario','=','mentor.codigo_usuario')
                                ->join('seguridad.usuario as emprendedor','mentoria.codigo_usuario','=','emprendedor.codigo_usuario')
                                ->join('core.asesoria','mentoria.codigo_asesoria','=','asesoria.codigo_asesoria')
                                ->join('core.entidad','asesoria.codigo_entidad','=','entidad.codigo_entidad')
                                ->join('seguridad.usuario as proyecto','entidad.codigo_usuario','=','proyecto.codigo_usuario')
                                ->get();
        foreach($mentores_institucion as $mentor){
            $codigo_mentor = $mentor->user_id;
            $correo_mentor = $mentor->correo_electronico;
            $codigo_sesion = $mentor->codigo_mentoria;
            $fecha_sesion = $mentor->fecha_mentoria;
            $correo_emprendedor = $mentor->correo_electronico;
            $proyecto = $mentor->nombre_usuario;
            
            \Log::info("correo elec: ".$correo_mentor." y codigo sesion ".$codigo_sesion." fecha: ".$fecha_sesion." Correo empresario: ".$correo_emprendedor." Proyecto: ".$proyecto."\n");
           
        }
        dd("termine"); 
    }

    public function ejemplo(){

        $filename = sprintf('%s_%s.xlsx', 'reporte_evento', date('YmdHms'));
			$filepath = sprintf('reports/%s/%s/%s', $institution, $user->codigo_usuario, $filename);
			$file = Excel::create($filename, function($excel) use($data, $dataSessionOfEvent) {
				$excel->sheet('Reporte evento', function($sheet)  use($data, $dataSessionOfEvent) {
					$sheet->setAllBorders('thin');
					$array = array('Nombres',
						'Apellidos',
						'Celular',
						'Correo Electronico',
						'Numero de Documento',
						'Tipo de identificacion',
						'Ciudad',
            'Fecha entrega certificado',
            'Certificado entregado',
            'Codigo certificado'
					);
					// Validate if exist Sessions

					if(count($dataSessionOfEvent) >= 1) {
						$num_sesion_ev = array();

						// Add to Array that Sessions

						foreach ($dataSessionOfEvent as $valueSesionEvent) {
							array_push($array, $valueSesionEvent->numero_sesion);
							array_push($num_sesion_ev, $valueSesionEvent->numero_sesion);
						}
						$sheet->rows(array(
							$array,
						));

						// Include rest data of Users

						foreach ($data as $value) {
							$arrayData = array();
							array_push($arrayData,
								$value->nombre_usuario,
								$value->primer_apellido_persona.' '.$value->segundo_apellido_persona,
								$value->movil_usuario,
								$value->correo_electronico,
								$value->identificacion_usuario,
								$value->nombre_tipo_identificacion,
								$value->nombre_ciudad,
                isset($value->fecha_certificado) ? $value->fecha_certificado : '',
                isset($value->certificado_evento) && $value->certificado_evento ? 'Entregado' : 'Sin entregar',
                isset($value->certificado_evento) && $value->certificado_evento ? $value->codigo_usuario_evento : ''
							);

							// Obtain sessions of Users with Explode

							$sesion_user = explode(',', $value->sesiones_usuario);
							if(isset($value->sesiones_usuario)) {
								$num_sesions = explode(',', $value->num_sesiones);

								/*
								  Init counter in 0 and validate value of Explode ( Session User ), If the index in array exist, and is equal to Session of Event show response if Assist of did not Assist, and increment the counter.
								*/

                $countSession = 0;
                foreach ($num_sesion_ev as $itemSession) {
                  if (isset($num_sesions[$countSession])) {
                    if ($num_sesions[$countSession] == $itemSession) {
                      array_push($arrayData, 'Asistio');

                      $countSession++;
                    } else {
                      array_push($arrayData, 'Sin asistencia');
                    }
                  } else {
                    array_push($arrayData, 'Sin asistencia');
                  }
                }
							}
							$sheet->rows(array(
								$arrayData
							));
						}
					} else {

					  // Validate if not exist Sessions in event

						array_push($array, 'Sesion');
						$sheet->rows(array(
							$array,
						));
						foreach ($data as $value) {
							$sheet->appendRow([
								$value->nombre_usuario,
                $value->primer_apellido_persona.' '.$value->segundo_apellido_persona,
								$value->movil_usuario,
								$value->correo_electronico,
								$value->identificacion_usuario,
								$value->nombre_tipo_identificacion,
								$value->nombre_ciudad,
								isset($value->fecha_certificado) ? $value->fecha_certificado : '',
								isset($value->certificado_evento) && $value->certificado_evento ? 'Entregado' : 'Sin entregar',
                isset($value->certificado_evento) && $value->certificado_evento ? $value->codigo_usuario_evento : '',
								'No hay sesiones asignadas para el evento'
							]);
						}
					}
				});
			})->string('xlsx');
			// Store file to s3
			Storage::put($filepath, $file, 'public');
			// File valid
			if(!Storage::has($filepath)) {
				throw new \Exception("Error recuperando xls reporte reto evento ($filepath).");
			}
			// Get url
			$fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);
			// Send mail
			$data = [];
			$data['_template'] = 'emails.report';
			$data['_subject'] = 'Reporte evento';
			$data['_to'] = $user->correo_electronico;
			$data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
			$data['_inst'] = $institution;
			$data['_url'] = $fileurl;
			SendEmail::notification($data);
    }
}