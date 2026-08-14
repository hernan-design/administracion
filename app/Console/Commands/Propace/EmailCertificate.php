<?php

namespace App\Console\Commands\propace;

use Illuminate\Console\Command;
// use Illuminate\Support\Facades\Storage;
use App\Models\Aprende\Curso,
    App\Models\Core\Estado,
    App\Models\Core\UsuarioEncuesta,
    App\Models\Aprende\InscripcionCurso;

use DB, SendEmail, Lang, Log, App, AWS;


class EmailCertificate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'propace:emailcerfiticate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envios de certificados de cursos';

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
        //total usuario q' completaron cursos
		$curseCompleted = InscripcionCurso::select(
			DB::raw('count(u.correo_electronico)'),
			'u.correo_electronico', 'u.codigo_usuario',
			'u.nombre_usuario','u.nickname_usuario',
			DB::raw('max(inscripcion_curso.fecha_inscripcion_curso) AS fecha_inscripcion_curso'),
			DB::raw('max(inscripcion_curso.codigo_inscripcion_curso) AS codigo_inscripcion_curso'),
			DB::raw('count(inscripcion_curso.fecha_envio_certificado) AS envio_certificado'),
			'pe.primer_apellido_persona'
        )
		->join( 'aprende.curso_institucion as ci', 'inscripcion_curso.codigo_curso', '=', 'ci.codigo_curso' )
		->join( 'seguridad.usuario as u', 'inscripcion_curso.codigo_usuario', '=', 'u.codigo_usuario' )
		->join( 'core.persona as pe', 'inscripcion_curso.codigo_usuario', '=', 'pe.codigo_usuario' )
		->where( 'ci.codigo_institucion', 75 )
		->where( 'ci.codigo_estado', 8 )
        ->whereIn('ci.codigo_curso', [263,265,266,264])
		->where( 'inscripcion_curso.porcentaje_progreso', 100 )
		->groupBy('u.correo_electronico', 'u.codigo_usuario', 'u.nombre_usuario','u.nickname_usuario', 'pe.primer_apellido_persona')
		->get();

        //total cursos por institucion propace
		$totalCurseInstitution = Curso::query();
		$totalCurseInstitution->select();
		$totalCurseInstitution->join('aprende.curso_institucion', function($join){
			$join->on('curso_institucion.codigo_curso', '=', 'curso.codigo_curso')
				->where('curso_institucion.codigo_institucion', 75)
                ->whereIn('curso_institucion.codigo_curso', [263,265,266,264])
				->where('curso_institucion.codigo_estado', '=', Estado::getState('active'));
		});

        foreach ($curseCompleted as $key => $value) {
            Log::error("Numero de cursos: ".$totalCurseInstitution->count()." Prueba ".$curseCompleted[ $key ]->count);
            if ( $curseCompleted[ $key ]->count == $totalCurseInstitution->count() ) {
                $testCompleto = $this->getCompleteTest( $value->codigo_usuario );
                Log::error(sprintf('%s:%s', 'test comleto', $testCompleto));
                if ( $testCompleto and $curseCompleted[$key]->envio_certificado == 0 ) {
                    $urlCertificado = $this->certificateTotal( $value );
                    InscripcionCurso::where( 'codigo_inscripcion_curso', $value->codigo_inscripcion_curso)
                        ->update(['fecha_envio_certificado' => date('Y-m-d')]);
                    // Sending Email
                    $data = [];
                    $data['_template'] = 'emails.program.propace.notificacion_certificado';
                    $data['_subject'] = 'Programa de formación a cuidadores';
                    // Data Template
                    $data['comment'] = Lang::get('program.mail.body.propaceCertificado');
                    $data['_to'] = $value->correo_electronico;
                    $data['_to_name'] = ucwords( strtolower( $value->nombre_usuario ) );
                    $data['_inst'] = 75;
                    $data['_url_action'] = sprintf("/%s/%s/%s", 'proyecto', $value->nickname_usuario,'convocatorias');
                    $data['_attach'] = $urlCertificado;
                    SendEmail::notification($data);

                    $message = ' Se envió certificado' . ' al Usuario ' . $value->codigo_usuario  ;
                    Log::info(sprintf('%s: %s', 'Commands/Propace/EmailCertificate', $message));
                    $this->info($message);
                }else {
                    $message = 'Usuario ' . $value->codigo_usuario  .  ' no ha completado test zarit 2 o ya se envió certificado';
                    Log::info(sprintf('%s: %s', 'Commands/Propace/EmailCertificate', $message));
                    $this->info($message);
                }
            }else {
                $message = 'Usuario ' . $value->codigo_usuario  . ' no ha completado los cursos';
                Log::info(sprintf('%s: %s', 'Commands/Propace/EmailCertificate', $message));
            }
        }
    }

    /* Function to get complete test zarit */

    public static function getCompleteTest( $userId ){
        $test1 = UsuarioEncuesta::select()
                ->where([
                    ['usuario_encuesta.codigo_encuesta', '=', 216],
                    ['usuario_encuesta.codigo_usuario', '=', $userId],
                    ['usuario_encuesta.codigo_estado', '=', Estado::getState('complete')],
                ]);

        $encuesta1 = $test1->get();
        $test2 = UsuarioEncuesta::select()
                ->where([
                    ['usuario_encuesta.codigo_encuesta', '=', 217],
                    ['usuario_encuesta.codigo_usuario', '=', $userId],
                    ['usuario_encuesta.codigo_estado', '=', Estado::getState('complete')],
                ]);

        $encuesta2 = $test2->get();
        if ( $encuesta1->count() > 0 && $encuesta2->count() > 0 )
            $encuesta = true;
        else
            $encuesta = false;
        return $encuesta;
    }


      	/* Certificate Total| */

      public static function certificateTotal( $user ) {

				//se usa array $meses ya que no toma configuraciòn de idioma
				$meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];

          $newDate = date('Y');
					$date = strftime(" de %Y", strtotime($newDate));
					$date = $meses[ date('n', strtotime( $newDate ) )-1] . $date ;

        $data = [];
        $data['title'] = "Certificado";
        $data['userName'] = $user->nombre_usuario . ' ' . $user->primer_apellido_persona;
        $data['course'] = 'Programa de formación a cuidadores';
        $data['date'] = $date;
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('reports.course.certificate_propace', $data))->setPaper('A4', 'landscape');

        $s3 = AWS::createClient('s3');

        $bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

        $filename = sprintf('%s_%s_%s.pdf', 'certificado_Programa de formación a cuidadores_', $user->codigo_usuario.'_', 75);
        $key = sprintf('%s/%s/%s/%s', 'documents', 'certificados', 'cursos', $filename);

        $result = $s3->putObject([
          'Bucket' =>  $bucket,
          'Key'    =>  $key,
          'Body'   =>  $pdf->output(),
          'ACL'    => 'public-read'
        ]);

        return $result['ObjectURL'];
    }
}
