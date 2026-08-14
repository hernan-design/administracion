<?php

namespace App\Console\Commands\Imports;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Log, Lang, Storage, Excel, DB, SendEmail, Hash, TuProyecto, DateTime, Config;

use App\Models\Seguridad\Usuario,
    App\Models\Seguridad\Rol,
    App\Models\Seguridad\RolUsuario,
    App\Models\Core\UsuarioInstitucion,
    App\Models\Core\Persona,
    App\Models\Param\Institucion,
    App\Models\Core\Estado,
    App\Models\Core\ClaseEmprendimientoInstitucion,
    App\Models\Core\ClaseEmprendimiento,
    App\Models\Core\Entidad,
    App\Models\Core\DatoEntidad,
    App\Models\Core\PrivacidadContenido,
    App\Models\Core\ProfesionPersona,
    App\Models\Core\OcupacionPersona,
    App\Console\Commands\LoadSurvey\CargaEncuesta,
    App\Console\Commands\LoadEvaluation\CargaEvaluacion,
    App\Console\Commands\LoadUsers\CargaUsuarios;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class LoadUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'general:loadusers {institution} {codigo_documento}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cargue de usuarios';


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
        Log::info('inicia descarga archivo');

        $institution = Institucion::find($this->argument('institution'));
        if(!$institution instanceof Institucion) {
            throw new \Exception("Error recuperando institucion {$this->argument('institution')}.");
        }

            $codigo_documento = $this->argument('codigo_documento');
            Log::info('LOAD USERS' . $codigo_documento);
        //Valida que documento va descargar.
        if ($codigo_documento == 17) { //Aplica para cargar usuarios (17)
            $nombre_archivo = 'cargue_usuarios_' . $institution->slug_institucion . '.xlsx';
        } elseif ($codigo_documento == 18) { //Aplica para cargar usuarios y entidades (18)
            $nombre_archivo = 'cargue_usuarios_entidades_' . $institution->slug_institucion . '.xlsx';
        }
        // $var = Storage::disk('s3')->exists($url);
        // $contents = Storage::disk('s3')->get($url);
        $type ='documents';
        $key = sprintf('%s/%d/%d/%s', $type, date('Y'), date('m'), $nombre_archivo);
        $bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');
        $s3 = \AWS::createClient('s3');

        $result = $s3->getObject([
            'Bucket' =>  $bucket,
            'Key'    =>  $key,
        ]);

        $ruta_archivo = $institution->slug_institucion . '/' . $nombre_archivo;
        try {
            Storage::disk('local')->put($ruta_archivo, $result['Body']);
        } catch (S3Exception $e) {
            $message = sprintf('%s/', $e->getMessage());
            Log::error($message);
            Log::error('Error al recuperar excel');
        }

        $path = '/storage/app/' . $ruta_archivo;

        // lectura del excel descargado
        // Excel::load($path, function($reader) {
        //     $results = $reader->get()->toArray();
        //     foreach($results as $key => $resul){
        //         Log::info($resul['entidad']);
                //user data

        //     }
        // });

        \Log::info("Finalizo descarga archivo");
    }

}
