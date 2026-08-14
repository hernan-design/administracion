<?php

namespace App\Console\Commands\Reports;

use Illuminate\Console\Command;
use App\Exports\ReportsExport;

use Log, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario,
    App\Models\Param\Institucion,
    App\Models\Core\Estado,
    App\Models\Param\TerminosCondiciones,
    App\Models\Seguridad\Rol,
    App\Models\Seguridad\RolUsuario,
    App\Models\Bita\LogActivity;

class RolsModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:rolsmodule {user} {institution} {filters}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte de roles en xls';

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
            $header = $data = [];

            // Valid arguments
            $user = Usuario::find($this->argument('user'));
            if(!$user instanceof Usuario) {
                throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
            }

            $institution = Institucion::find($this->argument('institution'));
            if(!$institution instanceof Institucion) {
                throw new \Exception("Error recuperando institucion {$this->argument('institution')}.");
            }

            $query = Usuario::query();
            $query->select(
                'usuario.codigo_usuario',
                DB::raw('lower(usuario.correo_electronico) as correo_electronico'),
                //'telefono_usuario',
                'movil_usuario',
                'persona.genero_persona',
                'usuario.identificacion_usuario',
                'aceptacion_terminos.fecha_aceptacion_terminos',
                'tipo_identificacion.nombre_tipo_identificacion',
                'usuario_institucion.promedio_evaluacion_mentor',
                'rol.title as rol',
                DB::raw("initcap( (COALESCE(usuario.nombre_usuario, '') || ' ' ||
                        COALESCE(persona.primer_apellido_persona, '') || ' ' ||
                        COALESCE(persona.segundo_apellido_persona, '')) ) as nombre_usuario"),
                DB::raw("(SELECT fecha_aceptacion_terminos FROM seguridad.aceptacion_terminos
                          JOIN param.version_terminos
                          ON aceptacion_terminos.codigo_version_terminos = version_terminos.codigo_version_terminos
                          JOIN param.terminos_condiciones
                          ON version_terminos.codigo_terminos_condiciones = terminos_condiciones.codigo_terminos_condiciones
                          WHERE tipo_terminos = '".TerminosCondiciones::getTypeTerms('privacy')."'
                          AND usuario_institucion.codigo_usuario_institucion = aceptacion_terminos.codigo_usuario_institucion
                          LIMIT 1
                          ) AS fecha_aceptacion_privacidad"),
                (( isset($this->argument('filters')['role']) && $this->argument('filters')['role']) ?
                    DB::raw('COALESCE(mentor.codigo_estado, 0) AS codigo_estado') : DB::raw('0 AS codigo_estado'))
            );

            $query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
            $query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
            $query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

            $query->leftJoin('core.tipo_identificacion', 'tipo_identificacion.codigo_tipo_identificacion', '=', 'usuario.tipo_identificacion_usuario');
            $query->leftJoin('core.nivel_educacion', 'nivel_educacion.codigo_nivel_educacion', '=', 'persona.codigo_nivel_educacion');

            //  User institution
            $query->join('core.usuario_institucion', function($join) use($institution){
                $join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
                    ->where('usuario_institucion.codigo_institucion', '=', $institution->codigo_institucion)
                    ->whereNotIn('usuario_institucion.codigo_estado', [Estado::getState('inactive'), Estado::getState('removed')]);
            });

            $query->join('seguridad.aceptacion_terminos', 'aceptacion_terminos.codigo_usuario_institucion', '=', 'usuario_institucion.codigo_usuario_institucion');
            $query->join('param.version_terminos', 'version_terminos.codigo_version_terminos', '=', 'aceptacion_terminos.codigo_version_terminos');
            $query->join('param.terminos_condiciones', 'terminos_condiciones.codigo_terminos_condiciones', '=', 'version_terminos.codigo_terminos_condiciones');

            // roles
            $query->join('seguridad.rol_usuario', function ($join) use($institution) {
                $join->on('usuario.codigo_usuario', '=', 'rol_usuario.user_id')
                    // ->where('rol_usuario.user_id', '=', 'usuario.codigo_usuario')
                     ->where('rol_usuario.codigo_institucion', $institution->codigo_institucion);
            });

            $query->join('seguridad.rol', function ($join) {
                $join->on('rol_usuario.role_id', '=', 'rol.id')
                     ->where('rol.id', '<>', Rol::getRol('superadmin'));
            });


            if( isset($this->argument('filters')['title']) && !empty($this->argument('filters')['title']) ) {
                $query->whereRaw("(COALESCE(trim(usuario.nombre_usuario), '') || ' ' || COALESCE(trim(persona.primer_apellido_persona), '') || ' ' || COALESCE(trim(persona.segundo_apellido_persona), '')) like '%".trim($this->argument('filters')['title'])."%'");
            }

            $query->where('terminos_condiciones.tipo_terminos', '=', TerminosCondiciones::getTypeTerms('terms'));
            $query->whereRaw('aceptacion_terminos.fecha_aceptacion_terminos is not null');
            $query->where('usuario.tipo_usuario', '=',  Usuario::typeUser('person'));
            $query->orderBy('usuario.nombre_usuario', 'asc');



            $usuarios = $query->get();


            // Prepare header
            $header = [];
            $header[] = 'Codigo';
            $header[] = 'Nombre';
            $header[] = 'Número de identificación';
            $header[] = 'Tipo de identificación';
            $header[] = 'Celular';
            $header[] = 'Correo Electrónico';
            $header[] = 'Rol';
            $header[] = 'Histórico';
            $header[] = 'Fecha de aceptación de términos y condiciones de usuario';
            $header[] = 'Fecha de aceptación de términos y condiciones de mentoría';

            $data = collect([]);
            $usersId = [];
            foreach ($usuarios as $usuario)
            {
                //$usuario->historico_log_rol =  'vacio';
                $usersId [] = $usuario->codigo_usuario;
                // $historico_log_rol = $this->getLogRol($usuario->codigo_usuario);

                // unset($record);
                // $record[] = $usuario->codigo_usuario;
                // $record[] = $usuario->nombre_usuario;
                // $record[] = $usuario->identificacion_usuario;
                // $record[] = $usuario->nombre_tipo_identificacion;
                // $record[] = $usuario->movil_usuario;
                // $record[] = $usuario->correo_electronico;
                // $record[] = $usuario->rol;
                // $record[] = "";
                // $record[] = $usuario->fecha_aceptacion_privacidad;
                // $record[] = $usuario->fecha_aceptacion_terminos;
                // //$record[] = $usuario->telefono_usuario;
                // //$record[] = $usuario->movil_usuario;
                // $data[] = $record;
            }

            $getLogRol = LogActivity::select('id', 'description', 'properties', 'subject_id', 'created_at')
                        ->whereIn('subject_id', $usersId)
                        ->where('codigo_institucion', 30)
                        ->where('description', 'like', 'activitylog.user.role.%')
                        ->orderBy('id', 'desc')
                        ->get();

            $dataInform = collect([]);
            foreach ($usuarios as $usuario) {
              $dataHistorico = [];
              foreach ($getLogRol as $key => $value) {
                if ($value->subject_id==$usuario->codigo_usuario) {
                 $dataHistorico[] = $value;
                }
              }
              $historico_log_rol = $this->getLogRol($dataHistorico);
              unset($record);
              $record[] = $usuario->codigo_usuario;
              $record[] = $usuario->nombre_usuario;
              $record[] = $usuario->identificacion_usuario;
              $record[] = $usuario->nombre_tipo_identificacion;
              $record[] = $usuario->movil_usuario;
              $record[] = $usuario->correo_electronico;
              $record[] = $usuario->rol;
              $record[] =  $historico_log_rol;
              $record[] = $usuario->fecha_aceptacion_privacidad;
              $record[] = $usuario->fecha_aceptacion_terminos;
              //$record[] = $usuario->telefono_usuario;
              //$record[] = $usuario->movil_usuario;
              $dataInform[] = $record;
            }


            // $historico_log_rol = $this->getLogRol($getLogRol);



            // Prepare file
            $filename = sprintf('%s_%s.xlsx', 'reporte_roles_', date('YmdHm'));
            $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

            $export = new ReportsExport([$header], [$dataInform]);

            // Genera el archivo Excel
            $file = Excel::store($export, $filepath, 's3', null, [
              'visibility' => 'public',
            ]);

            $fileUrl = Storage::disk('s3')->url($filepath);

            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = "Reporte roles";
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution->codigo_institucion;
            $data['_url'] = $fileUrl;
            SendEmail::notification($data);

        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'RolesModule:command', $e->getMessage()));
            abort(500);
        }
    }

    public function getLogRol( array $getLogRol) {

        if(count($getLogRol) > 0) {
            $historico_log_rol = '';
                foreach ($getLogRol as $key => $value) {

                    if ($value->properties) {
                        $historico_log_rol .=  "\n";
                        $historico_log_rol .= '- ';
                        $historico_log_rol .= $value->properties['attributes']['title'];
                    }

                    if ($value->description) {
                        $mystring = $value->description;
                        $findstring   = 'created';
                        $result = strpos($mystring, $findstring);

                        if ($result === false) {
                            $historico_log_rol .=  ' Eliminado ';
                        } else {
                            $historico_log_rol .= ' Asignado ';
                        }
                    }

                    if ($value->created_at) {
                        $historico_log_rol .= $value->created_at ;
                    }
                }
            return $historico_log_rol;
        }
    }

}
