<?php

namespace App\Console\Commands\Reports;

use Illuminate\Console\Command;
use App\Exports\ReportsExport;

use Log, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario, App\Models\Param\Institucion, App\Models\Core\Estado, App\Models\Param\TerminosCondiciones, App\Models\Seguridad\Rol;

class UsersModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:usersmodule {user} {institution} {filters}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte usuarios en xls';

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
                'telefono_usuario',
                'direccion_usuario',
                'movil_usuario',
                'usuario.nickname_usuario',
                'usuario.profesion_usuario',
                'persona.genero_persona',
                'ocupacion_persona.nombre_ocupacion_persona',
                'aceptacion_terminos.direccion_ip',
                'usuario.identificacion_usuario',
                'aceptacion_terminos.fecha_aceptacion_terminos',
                'nivel_educacion.nombre_nivel_educacion',
                'usuario.fecha_nacimiento_usuario',
                'tipo_identificacion.nombre_tipo_identificacion',
                DB::raw('initcap(nombre_ciudad) as nombre_ciudad'),
                'usuario_institucion.promedio_evaluacion_mentor',
                DB::raw('initcap(distrito_ciudad) nombre_departamento'),
                DB::raw("initcap( (COALESCE(usuario.nombre_usuario, '') || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) ) as nombre_usuario"),
                DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as ciudad"),
                DB::raw("(SELECT fecha_aceptacion_terminos FROM seguridad.aceptacion_terminos
                    JOIN param.version_terminos ON aceptacion_terminos.codigo_version_terminos = version_terminos.codigo_version_terminos
                    JOIN param.terminos_condiciones ON version_terminos.codigo_terminos_condiciones = terminos_condiciones.codigo_terminos_condiciones
                    WHERE tipo_terminos = '".TerminosCondiciones::getTypeTerms('privacy')."'
                    AND usuario_institucion.codigo_usuario_institucion = aceptacion_terminos.codigo_usuario_institucion
                    LIMIT 1
                    ) AS fecha_aceptacion_privacidad"),
                (( isset($this->argument('filters')['role']) && $this->argument('filters')['role']) ? DB::raw('COALESCE(mentor.codigo_estado, 0) AS codigo_estado') : DB::raw('0 AS codigo_estado'))
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

            $query->leftJoin('core.ocupacion_persona', function($join){
                $join->on('persona.codigo_ocupacion_persona', '=', 'ocupacion_persona.codigo_ocupacion_persona')
                    ->where('ocupacion_persona.codigo_estado', '=', Estado::getState('active'));
            });

            if( isset($this->argument('filters')['nombre_usuario']) && !empty($this->argument('filters')['nombre_usuario']) ) {
                $query->whereRaw("(COALESCE(trim(usuario.nombre_usuario), '') || ' ' || COALESCE(trim(persona.primer_apellido_persona), '') || ' ' || COALESCE(trim(persona.segundo_apellido_persona), '')) ilike '%".trim($this->argument('filters')['nombre_usuario'])."%'");
            }

            if( isset($this->argument('filters')['correo_electronico']) && !empty($this->argument('filters')['correo_electronico']) ) {
                $query->whereRaw("usuario.correo_electronico ilike '%".trim($this->argument('filters')['correo_electronico'])."%'");
            }

            if( isset($this->argument('filters')['nombre_ciudad']) && !empty($this->argument('filters')['nombre_ciudad']) ) {
                $query->where("usuario.codigo_ciudad", '=', $this->argument('filters')['nombre_ciudad']);
            }

            if( isset($this->argument('filters')['nickname_usuario']) && !empty($this->argument('filters')['nickname_usuario']) ) {
                $query->whereRaw("usuario.nickname_usuario ilike '%".trim($this->argument('filters')['nickname_usuario'])."%'");
            }

            // Filter Role
            if( isset($this->argument('filters')['role']) && !empty($this->argument('filters')['role']) ) {
                $query->join('seguridad.rol_usuario as mentor', function($join) {
                    $join->on('mentor.codigo_institucion', '=', 'usuario_institucion.codigo_institucion')
                        ->on('mentor.user_id', '=', 'usuario_institucion.codigo_usuario')
                        ->where('mentor.role_id', '=', is_numeric($this->argument('filters')['role']) ? $this->argument('filters')['role'] : Rol::getRol($this->argument('filters')['role']) );
                });

                if( isset($this->argument('filters')['codigo_estado']) && !empty($this->argument('filters')['codigo_estado']) ) {
                    $query->where('mentor.codigo_estado', '=', $this->argument('filters')['codigo_estado']);
                }

                $query->orderBy(DB::raw("(CASE mentor.codigo_estado WHEN ".Estado::getState('review')." THEN mentor.codigo_estado END)"));
            }

            $query->where('terminos_condiciones.tipo_terminos', '=', TerminosCondiciones::getTypeTerms('terms'));
            $query->whereRaw('aceptacion_terminos.fecha_aceptacion_terminos is not null');
            $query->where('usuario.tipo_usuario', '=',  Usuario::typeUser('person'));
            $query->orderBy('usuario.nombre_usuario', 'asc');

            $usuarios = $query->get();

            // Prepare header
            $header = [];
            $header[] = 'Nombre';
            $header[] = 'Número de identificación';
            $header[] = 'Tipo de identificación';
            $header[] = 'Sexo';
            $header[] = 'Correo Electrónico';
            $header[] = 'Nick Name';
            $header[] = 'Fecha de Nacimiento';
            $header[] = 'Nivel de educación';
            $header[] = 'Ocupación';
            $header[] = 'Profesión';
            $header[] = 'Departamento';
            $header[] = 'Municipio';
            $header[] = 'Fecha Aceptación de Términos';
            $header[] = 'Fecha Aceptación de Privacidad';
            $header[] = 'Direccion IP';
            $header[] = 'Teléfono';
            $header[] = 'Celular';
            $header[] = 'Dirección';

            $data = collect([]);
            foreach ($usuarios as $usuario)
            {
                unset($record);
                $record[] = $usuario->nombre_usuario;
                $record[] = $usuario->identificacion_usuario;
                $record[] = $usuario->nombre_tipo_identificacion;
                $record[] = isset(Usuario::$gender[$usuario->genero_persona]) ? Usuario::$gender[$usuario->genero_persona]: '';
                $record[] = $usuario->correo_electronico;
                $record[] = $usuario->nickname_usuario;
                $record[] = $usuario->fecha_nacimiento_usuario;
                $record[] = $usuario->nombre_nivel_educacion;
                $record[] = $usuario->nombre_ocupacion_persona;
                $record[] = $usuario->profesion_usuario;
                $record[] = $usuario->nombre_departamento;
                $record[] = $usuario->nombre_ciudad;
                $record[] = $usuario->fecha_aceptacion_terminos;
                $record[] = $usuario->fecha_aceptacion_privacidad;
                $record[] = $usuario->direccion_ip;
                $record[] = $usuario->telefono_usuario;
                $record[] = $usuario->movil_usuario;
                $record[] = $usuario->direccion_usuario;
                $data[] = $record;
            }

            // Prepare file
            $filename = sprintf('%s_%s.xlsx', 'usuarios', date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

            $export = new ReportsExport([$header], [$data]);

            // Genera el archivo Excel
            $file = Excel::store($export, $filepath, 's3', null, [
              'visibility' => 'public',
            ]);

            $fileUrl = Storage::disk('s3')->url($filepath);

            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = "Reporte usuarios";
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution->codigo_institucion;
            $data['_url'] = $fileUrl;
            SendEmail::notification($data);

        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'UsersModule:command', $e->getMessage()));
            abort(500);
        }
    }
}
