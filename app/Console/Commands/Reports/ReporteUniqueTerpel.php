<?php

namespace App\Console\Commands\Reports;

use App\Models\Param\Institucion, App\Models\Core\RespuestaEncuesta, App\Models\Core\ResultadoEvaluation, App\Models\Seguridad\Usuario, App\Models\Core\Estado, App\Models\Core\RegistroFaseUsuario, App\Models\Core\Convocatoria;
use Log, Storage, Excel, DB, SendEmail;
use Illuminate\Console\Command;

class ReporteUniqueTerpel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:terpelunique {institution} {user} {program}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte unico de Terpel';

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
            // Valid user
            $user = Usuario::find($this->argument('user'));
            if(!$user instanceof Usuario) {
                throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
            }

            // Valid Program
            $program = Convocatoria::find($this->argument('program'));
            if(!$program instanceof Convocatoria) {
                throw new \Exception("Error recuperando convocatoria {$this->argument('program')}.");
            }

            $query = RegistroFaseUsuario::query();
            $query->select('registro_convocatoria.codigo_usuario_encuesta', 'rfu_inscription.codigo_registro_fase_usuario as rfu_ins_code', 'rfu_evaluation.codigo_registro_fase_usuario as rfu_eval_code', 'rfu_finalists.codigo_registro_fase_usuario as rfu_fin_code', 'ciudad.nombre_ciudad as city', 'ciudad.distrito_ciudad as region', 'registro_convocatoria.numero_registro_convocatoria as application', 'entity.nombre_usuario as proyect', 'userOwner.nombre_usuario as teacher');
            $query->join('core.registro_convocatoria', 'registro_convocatoria.codigo_registro_convocatoria', '=', 'registro_fase_usuario.codigo_registro_convocatoria');
            // Entity
            $query->join('seguridad.usuario as entity', 'entity.codigo_usuario', '=', 'registro_fase_usuario.codigo_usuario');
            $query->leftJoin('core.ciudad', 'ciudad.codigo_ciudad', '=', 'entity.codigo_ciudad');

            // User Owner
            $query->join('core.entidad', 'entidad.codigo_usuario', '=', 'registro_fase_usuario.codigo_usuario');
            $query->join('seguridad.usuario as userOwner', 'userOwner.codigo_usuario', '=', 'entidad.seg_codigo_usuario');

            // Phase ( INSCRIPTION )
            $query->join('core.registro_fase_usuario as rfu_inscription', function($join) {
                $join->on('rfu_inscription.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria')
                    ->where('rfu_inscription.codigo_fase', 237);
            });

            // Phase ( EVALUATION )
            $query->leftJoin('core.registro_fase_usuario as rfu_evaluation', function($join) {
                $join->on('rfu_evaluation.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria')
                    ->where('rfu_evaluation.codigo_fase', 272);
            });

            // Phase ( FINALISTS )
            $query->leftJoin('core.registro_fase_usuario as rfu_finalists', function($join) {
                $join->on('rfu_finalists.codigo_registro_convocatoria', '=', 'registro_convocatoria.codigo_registro_convocatoria')
                    ->where('rfu_finalists.codigo_fase', 273);
            });

            $query->where('registro_convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
            // States filter
            $query->whereIn('registro_fase_usuario.codigo_estado', [
                Estado::getState('active'), Estado::getState('no_linked'), Estado::getState('returned'), Estado::getState('rejected')
            ]);
            $query->orderBy('registro_convocatoria.numero_registro_convocatoria');

            $data = $query->get();

            // Prepare file
            $filename = sprintf('%s_%s.xlsx', 'reporte_unico_terpel', date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', Institucion::institution('terpel'), $user->codigo_usuario, $filename);

            $file = Excel::create($filename, function($excel) use($data) {
                $excel->sheet('Reporte unico Terpel', function($sheet) use($data) {

                    // Set all Borders
                    $sheet->setAllBorders('thin');

                    // Disable auto size for columns
                    $sheet->setAutoSize(array(
                        'B', 'C', 'D', 'E'
                    ));

                    $sheet->setWidth(array(
                        'A'     =>  20, 'F'     =>  20, 'G'     =>  20, 'H'     =>  20, 'I'     =>  20,
                        'J'     =>  20, 'K'     =>  20, 'L'     =>  20, 'M'     =>  20, 'N'     =>  20,
                        'O'     =>  20, 'P'     =>  20, 'Q'     =>  20, 'R'     =>  20, 'S'     =>  20,
                        'T'     =>  20, 'U'     =>  20, 'V'     =>  20, 'W'     =>  20, 'X'     =>  20
                    ));

                    // One level titles
                    $sheet->appendRow([
                        'Aplicacion', 'Proyecto', 'Docente Lider', 'Institución Educativa', 'Ciudad de Institucion', 'FASE 1 - ( INSCRIPCION )', '', '', '', '', '', '', '', '', 'FASE 2 - ( EVALUACION )', '', '', '', '', '', '', '', '', 'FASE 3 - ( FINALISTAS )'
                    ]);

                    $sheet->mergeCells('F1:N1');
                    $sheet->mergeCells('O1:W1');

                    $sheet->cells('A1:E1', function($cells) {
                        $cells->setBackground('#F5C691');
                    });

                    $sheet->cells('F1:N1', function($cells) {
                        $cells->setAlignment('center');
                        $cells->setBackground('#799FCB');
                    });

                    $sheet->cells('O1:W1', function($cells) {
                        $cells->setAlignment('center');
                        $cells->setBackground('#F9665E');
                    });

                    $sheet->cells('X1', function($cells) {
                        $cells->setAlignment('center');
                        $cells->setBackground('#90C978');
                    });

                    // Two level titles
                    $sheet->appendRow([
                        '', '', '', '', '',
                        // Headers First Phase ( INSCRIPCION )
                        'SIENTE',
                        'IMAGINA',
                        'HAZ',
                        'COMPARTE',
                        'TRANSFORMACION',
                        'SOSTENIBILIDAD',
                        'INNOVACION',
                        'IMPACTO SOCIAL',
                        'PUNTAJE TOTAL',
                        // Headers Second Phase
                        'SIENTE',
                        'IMAGINA',
                        'HAZ',
                        'COMPARTE',
                        'TRANSFORMACION',
                        'SOSTENIBILIDAD',
                        'INNOVACION',
                        'IMPACTO SOCIAL',
                        'PUNTAJE TOTAL',
                        // Headers Three Phase
                        'PUNTAJE TOTAL'
                    ]);

                    $sheet->cells('F2:N2', function($cells) {
                        $cells->setBackground('#799FCB');
                    });

                    $sheet->cells('O2:W2', function($cells) {
                        $cells->setBackground('#F9665E');
                    });

                    $sheet->cells('X2', function($cells) {
                        $cells->setBackground('#90C978');
                    });

                    // Merge Columns
                    $sheet->setMergeColumn([ 'columns' => ['A','B','C','D','E'], 'rows' => [ [1,2] ] ]);

                    foreach ($data as $value) {
                        $sheet->appendRow([
                            $value->application,
                            $value->proyect,
                            $value->teacher,
                            $this->getSurveyDates($value->codigo_usuario_encuesta, 'INSTITUTION')->texto_respuesta_encuesta,
                            $value->city.' - '.$value->region,
                            // SIENTE ( FIRST PHASE )
                            $this->getScoreEvaluation($value->rfu_ins_code, [197, 198, 199])->total,
                            // IMAGINA ( FIRST PHASE )
                            $this->getScoreEvaluation($value->rfu_ins_code, [200, 201, 202])->total,
                            // HAZ ( FIRST PHASE )
                            $this->getScoreEvaluation($value->rfu_ins_code, [204, 203, 205, 206, 207])->total,
                            // COMPARTE ( FIRST PHASE )
                            $this->getScoreEvaluation($value->rfu_ins_code, [208, 209, 210])->total,
                            // TRANSFORMACION ( FIRST PHASE )
                            $this->getScoreEvaluation($value->rfu_ins_code, [211, 212])->total,
                            // SOSTENIBILIDAD ( FIRST PHASE )
                            $this->getScoreEvaluation($value->rfu_ins_code, [213, 214, 215, 216])->total,
                            // INNOVACION ( FIRST PHASE )
                            $this->getScoreEvaluation($value->rfu_ins_code, [217, 218])->total,
                            // IMPACTO SOCIAL ( FIRST PHASE )
                            $this->getScoreEvaluation($value->rfu_ins_code, [219, 220, 221, 222, 223])->total,
                            // TOTAL ( FIRST PHASE )
                            $this->getScoreEvaluation($value->rfu_ins_code, [])->total,
                            // SIENTE ( SECOND PHASE )
                            $this->getScoreEvaluation($value->rfu_eval_code, [224, 225, 226])->total,
                            // IMAGINA ( SECOND PHASE )
                            $this->getScoreEvaluation($value->rfu_eval_code, [227, 228, 229])->total,
                            // HAZ ( SECOND PHASE )
                            $this->getScoreEvaluation($value->rfu_eval_code, [230, 231, 232, 233, 234])->total,
                            // COMPARTE ( SECOND PHASE )
                            $this->getScoreEvaluation($value->rfu_eval_code, [235, 236, 237])->total,
                            // TRANSFORMACION ( SECOND PHASE )
                            $this->getScoreEvaluation($value->rfu_eval_code, [238, 239])->total,
                            // SOSTENIBILIDAD ( SECOND PHASE )
                            $this->getScoreEvaluation($value->rfu_eval_code, [240, 241, 242, 243])->total,
                            // INNOVACION ( SECOND PHASE )
                            $this->getScoreEvaluation($value->rfu_eval_code, [244, 245])->total,
                            // IMPACTO SOCIAL ( SECOND PHASE )
                            $this->getScoreEvaluation($value->rfu_eval_code, [246, 247, 248, 249, 250])->total,
                            // TOTAL ( SECOND PHASE )
                            $this->getScoreEvaluation($value->rfu_eval_code, [])->total,
                            // TOTAL ( THREE PHASE )
                            $this->getScoreEvaluation($value->rfu_fin_code, [])->total,
                        ]);
                    }
                });
            })->string('xlsx');

             // Store file to s3
            Storage::put($filepath, $file, 'public');

            // File valid
            if(!Storage::has($filepath)) {
                throw new \Exception("Error recuperando xlsx reporte unico terpel File($filepath).");
            }

             // Get url
            $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);

            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = 'Reporte reto 3';
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = Institucion::institution('innpulsa');
            $data['_url'] = $fileurl;
            SendEmail::notification($data);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'UniqueTerpel:Command', $e->getMessage()));
            abort(500);
        }
    }

    public function getSurveyDates($userSurvey, String $action) {
        $query = RespuestaEncuesta::query();
        switch ($action) {
            case 'INSTITUTION':
                $query->select('respuesta_encuesta.texto_respuesta_encuesta');
                $query->where('respuesta_encuesta.codigo_pregunta_encuesta', 4005);
            break;
        }
        $query->where('respuesta_encuesta.codigo_usuario_encuesta', $userSurvey);
        $query->where('respuesta_encuesta.codigo_estado', Estado::getState('active'));

        return $query->first();
    }

    public function getScoreEvaluation($register, array $items) {
        $query = ResultadoEvaluation::query();
        $query->select(DB::raw('SUM ( evaluacion_item_emprendimiento.puntaje_resul_eval_empren ) AS total'));
        if (!empty($items)) {
            $query->whereIn('evaluacion_item_emprendimiento.codigo_item_evaluacion', $items);
        }
        $query->join('core.asignacion_jurado', 'asignacion_jurado.codigo_asignacion_jurado', '=', 'resultado_evaluacion_emprendimiento.codigo_asignacion_jurado');
        $query->join('core.evaluacion_item_emprendimiento', 'evaluacion_item_emprendimiento.codigo_resultado_evaluacion', '=', 'resultado_evaluacion_emprendimiento.codigo_resultado_evaluacion');
        $query->where('asignacion_jurado.codigo_registro_fase_usuario', '=', $register);

        return $query->first();
    }
}
