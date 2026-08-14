<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

use  App\Models\Seguridad\Usuario, App\Models\Seguridad\Modulo, App\Models\Core\Convocatoria, App\Models\Core\Encuesta, App\Models\Core\Estado, App\Models\Core\Ciudad, App\Models\Core\SectorInteres, App\Models\Core\NivelEducacion, App\Models\Core\RegistroFaseUsuario, App\Models\Core\RespuestaEncuesta;

use Custom\Innpulsa, App\Models\Param\Institucion;

use Log, Storage, Excel, DB, SendEmail;

class ReportReto4 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'innpulsa:reto4 {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte emprendimientos reto 4 Aldea iNNpulsa';

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

            $header = $data = $matriz = [];

            // Valid user
            $user = Usuario::find($this->argument('user'));
            if(!$user instanceof Usuario) {
                throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
            }

            $program = Convocatoria::find(Innpulsa::$challenge_reto1);
            if(!$program instanceof Convocatoria){
                throw new \Exception("Error recuperando programa {$this->argument('program')}.");
            }

            $survey = Encuesta::find(Innpulsa::$survey_reto4);
            if(!$survey instanceof Encuesta){
                throw new \Exception("Error recuperando encuesta {$program->codigo_encuesta}.");
            }

            // Preparar preguntas
            $cities = [];
            $citys = Ciudad::select('ciudad.*')->where('ciudad.codigo_estado', Estado::getState('active'))->get();
            foreach ($citys as $city) {
              $cities["{$city->codigo_ciudad}"] = $city;
            }
            // Preparar sectores
            $sectors = SectorInteres::pluck('nombre_sector_interes', 'codigo_sector_interes')->toArray();

            // Preparar Nivel de educación
            $levels = NivelEducacion::pluck('nombre_nivel_educacion', 'codigo_nivel_educacion')->toArray();

            // Recuperar preguntas
            $arQuestions = $survey->getSurveyQuestions();

            // Prepare data
            $query = RegistroFaseUsuario::query();

            $query->select('fecha_usuario_encuesta','hora_usuario_encuesta','ue.nombre_usuario as nombre_proyecto', 'ue.identificacion_usuario as nit', 'uo.nombre_usuario as nombre_usuario', 'persona.primer_apellido_persona','persona.segundo_apellido_persona','tipo_identificacion.nombre_tipo_identificacion as tipo_identificacion_usuario','uo.identificacion_usuario as identificacion_usuario','uo.movil_usuario as movil_usuario','uo.telefono_usuario as telefono_usuario','usrenc.codigo_usuario_encuesta as codigo_usuario_encuesta');
            $query->join('core.registro_convocatoria', 'registro_fase_usuario.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria');
            $query->join('seguridad.usuario as ue', 'registro_convocatoria.codigo_usuario', '=', 'ue.codigo_usuario');
            $query->join('core.entidad', 'ue.codigo_usuario', '=', 'entidad.codigo_usuario');
            $query->join('seguridad.usuario as uo', 'entidad.seg_codigo_usuario', '=', 'uo.codigo_usuario');
            $query->join('core.persona', 'uo.codigo_usuario', '=', 'persona.codigo_usuario');
            $query->leftJoin('core.tipo_identificacion as tipo_identificacion','uo.tipo_identificacion_usuario','=','tipo_identificacion.codigo_tipo_identificacion');
            $query->join('core.usuario_encuesta as usrenc', function ($join) {
                $join->on('usrenc.codigo_usuario', '=', 'ue.codigo_usuario')
                ->where('usrenc.codigo_estado',Estado::getState('complete'))
                ->where('usrenc.codigo_encuesta', Innpulsa::$survey_reto4)
                ->where('usrenc.codigo_modulo', Modulo::getModule('phases'))
                ->where('usrenc.id_recurso',Innpulsa::$stage_challenge_4);
            });
            $query->where('registro_convocatoria.codigo_convocatoria', '=', Innpulsa::$challenge_reto1);
            $query->where('registro_fase_usuario.codigo_fase', '=', Innpulsa::$stage_challenge_4);
            $query->where('core.registro_fase_usuario.codigo_estado','=', Estado::getState('active'));
            $query->orderBy('fecha_usuario_encuesta','desc');
            $query->orderBy('hora_usuario_encuesta','desc');
            $usuarios = $query->get();

            // Prepare header
            $header = [];
            $header[] = 'Nombre Proyecto / Empresa'; 
            $header[] = 'NIT';
            $header[] = 'Nombre Usuario';             
            $header[] = 'Primer Apellido'; 
            $header[] = 'Segundo Apellido'; 
            $header[] = 'Tipo Identificacion Usuario'; 
            $header[] = 'Identificacion Usuario'; 
            $header[] = 'Telefono Usuario'; 
            $header[] = 'Movil Usuario';
            $header[] = 'Fecha de la Encuesta';
            $header[] = 'Hora de la Encuesta'; 
            
            $result = RespuestaEncuesta::buildReportHeaders($arQuestions, $header, $matriz);
            $header = $result->header;
            $matriz = $result->matriz;

            // Prepare answers
            $answers = RespuestaEncuesta::buildReportAnswers($usuarios->pluck('codigo_usuario_encuesta')->all());
                //Prepare data
               $data = collect([]);
                    
                foreach ($usuarios as $register)
                {
                    unset($record);
                    $record[] = $register->nombre_proyecto;
                    $record[] = $register->nit;
                    $record[] = $register->nombre_usuario;
                    $record[] = $register->primer_apellido_persona;
                    $record[] = $register->segundo_apellido_persona;
                    $record[] = $register->tipo_identificacion_usuario;
                    $record[] = preg_replace('/\./', '', $register->identificacion_usuario);
                    $record[] = $register->telefono_usuario;
                    $record[] = $register->movil_usuario;
                    $record[] = $register->fecha_usuario_encuesta;
                    $record[] = $register->hora_usuario_encuesta;
                    
                    // Prepare survey answers
                    $record = RespuestaEncuesta::buildAnswers($arQuestions, $answers, $matriz, $cities, $sectors, $levels, $register->codigo_usuario_encuesta, $record, false);
                    
                    $data[] = $record;
                }

            // Prepare file
            $filename = sprintf('%s_%s.xlsx', 'reporte_reto4', date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', Institucion::institution('innpulsa'), $user->codigo_usuario, $filename);

            $file = Excel::create($filename, function($excel) use($header, $data) {
                // Set the title
                $excel->setTitle('Reporte reto 4');

                // Set sheet alignement
                $excel->getDefaultStyle()
                        ->getAlignment()
                        ->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $excel->sheet('Reporte reto 4', function($sheet) use($header, $data) {
                    
                    // borders
                    $sheet->setAllBorders('thin');
                    
                    // Font family
                    $sheet->setFontFamily('Calibri');
                   
                    // Set auto size for sheet
                    $sheet->setAutoSize(true);

                     // Titles
                    $sheet->appendRow($header);
                    $lastHeaderColumm = $sheet->getHighestColumn();
                    
                    // Data
                    $sheet->rows($data);
                    
                    // Add First Row with title
                    $sheet->prependRow(1,['RETO 4']);
   
                    // Set Row Styles:
                    $sheet->row(1, function($row) {
                        $row->setBackground('#A9F5F2');
                        $row->setAlignment('center');
                        
                        // Set font
                        $row->setFont(array(
                            'size'       => '14'
                        ));
                    });

                    $sheet->cells('A2:'.$lastHeaderColumm.'2', function($cells) {
                        $cells->setBackground('#A9F5F2');
                        $cells->setAlignment('center');
                        
                        // Set font
                        $cells->setFont(array(
                            'size'       => '12'
                        ));
                    });

                    $lastColumm = $sheet->getHighestColumn();    
                    $colummnRange = 'A1:'.$lastColumm.'1';
                    $sheet->mergeCells($colummnRange);
                });
            })->string('xlsx');

            Storage::put($filepath, $file, 'public');

            // File valid
            if(!Storage::has($filepath)) {
                throw new \Exception("Error recuperando xlsx reporte reto 4 File($filepath).");
            }

            // Get url
            $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);

            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = 'Reporte reto 4';
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = Institucion::institution('innpulsa');
            $data['_url'] = $fileurl;
            SendEmail::notification($data);
            
        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReportReto4:Command', $e->getMessage()));
            abort(500);
        }
        
    }
}
