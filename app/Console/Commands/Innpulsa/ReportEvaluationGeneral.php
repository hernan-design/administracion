<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;
use Custom\Innpulsa;
use App\Models\Core\Convocatoria, App\Models\Core\Encuesta, App\Models\Param\Institucion, App\Models\Seguridad\Usuario;
use Log, Storage, Excel, DB, SendEmail;

class ReportEvaluationGeneral extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:evaluationgeneral {user} {institution} {program}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte de evaluaciones generales de estado 29 en detalle_voucher';

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
        try {
            // Valid arguments
            $user = Usuario::find($this->argument('user'));
            if(!$user instanceof Usuario) {
                throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
            }

            $institution = Institucion::find($this->argument('institution'));
            if(!$institution instanceof Institucion) {
                throw new \Exception("Error recuperando institucion {$this->argument('institution')}.");
            }

            $program = Convocatoria::find($this->argument('program'));
            if(!$program instanceof Convocatoria){
                throw new \Exception("Error recuperando programa {$this->argument('program')}.");
            }

            $survey = Encuesta::find(113);
            if(!$survey instanceof Encuesta){
                throw new \Exception("Error recuperando encuesta {$program->codigo_encuesta}.");
            }

            // Recuperar preguntas
            $arQuestions = $survey->getSurveyQuestions();

            $result = Innpulsa::getResultEvaluationGeneralAldea($arQuestions, $program);
            $header = $result->headers;
            $data = $result->data;

            // Prepare file
            $filename = sprintf('%s_%s.xlsx', 'evaluacion_general', date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);
           
            $file = Excel::create($filename, function($excel) use($header, $data) {
                $excel->sheet('Evaluacion General', function($sheet) use($header, $data) {
                    // Titles
                    $sheet->appendRow($header);
                    // Data
                    $sheet->rows($data);
                });
            })->string('xlsx');


            // Store file to s3
            Storage::put($filepath, $file, 'public');

            // File valid
            if(!Storage::has($filepath)) {
                throw new \Exception("Error recuperando xlsx reporte registros convocatoria File($filepath).");
            }

            // Get url
            $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);

            // Send mail
            // $data = [];
            // $data['_template'] = 'emails.report';
            // $data['_subject'] = "Reporte aplicaciones $program->nombre_convocatoria";
            // $data['_to'] = $user->correo_electronico;
            // $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            // $data['_inst'] = $institution->codigo_institucion;
            // $data['_url'] = $fileurl;
            // SendEmail::notification($data);
        } catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'innpulsa:command', $e->getMessage()));
            abort(500);
        }
    }
}
