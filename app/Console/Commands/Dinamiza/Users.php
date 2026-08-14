<?php

namespace App\Console\Commands\Dinamiza;

use Illuminate\Console\Command;

use Log, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario, App\Models\Param\Institucion, App\Models\Core\Estado, App\Models\Seguridad\Rol;

class Users extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dinamiza:users {user} {institution} {filters}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte usuarios de dinamiza en xlsx';

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
            $filters = $this->argument('filters');

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
            $query->select('movil_usuario', 'usuario.codigo_usuario', 'genero_persona', 'ciudad.nombre_ciudad', 'fecha_nacimiento_usuario', 'nombre_tipo_identificacion', 'identificacion_usuario', 'estado_civil_persona', 'up.nombre_ciudad as ciudad_residencia', 'nombrelocalidad_ciudad', 'nombre_barrio_localidad', 'nombre_estrato', 'nombre_tipo_vivienda', 'jefe_hogar', 'nombre_lista_ocupacion', 'nombre_sector_trabajo', 'nombre_nivel_educacion', 'nombre_autodenominacion', 'nombre_grupo_poblacional', 'nombre_grupo_comunitario', 'nombre_grupo_social', 'discapacidad', 'nombre_nivel_idioma',
                DB::raw("initcap( usuario.nombre_usuario) as nombre_usuario"),
                DB::raw("initcap( persona.primer_apellido_persona) as primer_apellido_persona"),
                DB::raw("initcap( persona.segundo_apellido_persona) as segundo_apellido_persona"),
                DB::raw("(CASE WHEN 
                    (
                        SELECT entidad.codigo_entidad
                        FROM core.entidad
                        WHERE entidad.seg_codigo_usuario = usuario.codigo_usuario LIMIT 1
                    ) IS NOT NULL THEN 'Si' ELSE 'No' END) AS enterprise")
            );

            $query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
            $query->leftJoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
            $query->leftJoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

            //  User institution
            $query->join('core.usuario_institucion', function($join) use($institution){
                $join->on('usuario.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
                    ->where('usuario_institucion.codigo_institucion', '=', $institution->codigo_institucion)
                    ->whereNotIn('usuario_institucion.codigo_estado', [Estado::getState('inactive'), Estado::getState('removed')]);
            });

            $query->leftJoin('core.tipo_identificacion', 'usuario.tipo_identificacion_usuario', 'tipo_identificacion.codigo_tipo_identificacion');
            $query->leftJoin('core.nivel_educacion', 'persona.codigo_nivel_educacion', 'nivel_educacion.codigo_nivel_educacion');
            $query->leftJoin('core.sector_trabajo', 'persona.codigo_sector_trabajo', 'sector_trabajo.codigo_sector_trabajo');
            $query->leftJoin('core.ciudad as up', 'persona.cor_codigo_ciudad', 'up.codigo_ciudad');
            $query->leftJoin('core.localidad_ciudad', 'usuario.codigo_localidad_ciudad', 'localidad_ciudad.codigo_localidad_ciudad');
            $query->leftJoin('core.barrio_localidad', 'usuario.codigo_barrio_localidad', 'barrio_localidad.codigo_barrio_localidad');
            $query->leftJoin('core.estrato', 'persona.codigo_estrato', 'estrato.codigo_estrato');
            $query->leftJoin('core.tipo_vivienda', 'persona.codigo_tipo_vivienda', 'tipo_vivienda.codigo_tipo_vivienda');
            $query->leftJoin('core.lista_ocupacion', 'persona.codigo_lista_ocupacion', 'lista_ocupacion.codigo_lista_ocupacion');
            $query->leftJoin('core.nivel_idioma', 'persona.codigo_nivel_idioma', 'nivel_idioma.codigo_nivel_idioma');
            $query->leftJoin('core.autodenominacion', 'persona.codigo_autodenominacion', 'autodenominacion.codigo_autodenominacion');
            $query->leftJoin('core.grupo_poblacional', 'persona.codigo_grupo_poblacional', 'grupo_poblacional.codigo_grupo_poblacional');
            $query->leftJoin('core.grupo_comunitario', 'persona.codigo_grupo_comunitario', 'grupo_comunitario.codigo_grupo_comunitario');
            $query->leftJoin('core.grupo_social', 'persona.codigo_grupo_social', 'grupo_social.codigo_grupo_social');
            
            if( isset($filters['codigo_convocatoria']) && !empty($filters['codigo_convocatoria']) ) {
                $query->join('core.registro_fase_usuario', 'usuario.codigo_usuario', '=', 'registro_fase_usuario.codigo_usuario');
                $query->join('core.fase', 'fase.codigo_fase', '=', 'registro_fase_usuario.codigo_fase');
                $query->join('core.convocatoria', 'fase.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
                $query->where('convocatoria.codigo_convocatoria', '=', $filters['codigo_convocatoria']);
            }

            if( isset($filters['fecha_nacimiento_usuario']) && !empty($filters['fecha_nacimiento_usuario']) ) {
                $query->where("usuario.fecha_nacimiento_usuario", '=', $filters['fecha_nacimiento_usuario']);
            }
            
            if( isset($filters['codigo_nivel_educacion']) && !empty($filters['codigo_nivel_educacion']) ) {
                $query->where("persona.codigo_nivel_educacion", '=', $filters['codigo_nivel_educacion']);
            }
            
            if( isset($filters['codigo_lista_ocupacion']) && !empty($filters['codigo_lista_ocupacion']) ) {
                $query->where("persona.codigo_lista_ocupacion", '=', $filters['codigo_lista_ocupacion']);
            }

            if( isset($filters['codigo_ciudad_usuario']) && !empty($filters['codigo_ciudad_usuario']) ) {
                $query->where("usuario.codigo_ciudad", '=', $filters['codigo_ciudad_usuario']);
            }
            
            if( isset($filters['codigo_localidad_ciudad']) && !empty($filters['codigo_localidad_ciudad']) ) {
                $query->where("usuario.codigo_localidad_ciudad", '=', $filters['codigo_localidad_ciudad']);
            }

            if( isset($filters['codigo_barrio_localidad']) && !empty($filters['codigo_barrio_localidad']) ) {
                $query->where("usuario.codigo_barrio_localidad", '=', $filters['codigo_barrio_localidad']);
            }
            
            if( isset($filters['codigo_estrato']) && !empty($filters['codigo_estrato']) ) {
                $query->where("persona.codigo_estrato", '=', $filters['codigo_estrato']);
            }
            
            if( isset($filters['codigo_tipo_vivienda']) && !empty($filters['codigo_tipo_vivienda']) ) {
                $query->where("persona.codigo_tipo_vivienda", '=', $filters['codigo_tipo_vivienda']);
            }
            
            if( isset($filters['jefe_hogar']) && !empty($filters['jefe_hogar']) ) {
                $query->where("persona.jefe_hogar", '=', $filters['jefe_hogar']);
            }

            if( isset($filters['codigo_sector_trabajo']) && !empty($filters['codigo_sector_trabajo']) ) {
                $query->where("persona.codigo_sector_trabajo", '=', $filters['codigo_sector_trabajo']);
            }

            if( isset($filters['enterprise']) && !empty($filters['enterprise']) ) {
                if($filters['enterprise'] == 'true'){
                    $query->whereIn('usuario.codigo_usuario', function($query)
                    {
                        $query->select(DB::raw('distinct "usuario"."codigo_usuario"
                        from "core"."entidad"
                        where "entidad"."seg_codigo_usuario" = "usuario"."codigo_usuario"'));
                    });
                }

                if($filters['enterprise'] == 'false'){
                    $query->whereNotIn('usuario.codigo_usuario', function($query)
                    {
                        $query->select(DB::raw('distinct "usuario"."codigo_usuario"
                        from "core"."entidad"
                        where "entidad"."seg_codigo_usuario" = "usuario"."codigo_usuario"'));
                    });
                }
            }
            
            if( isset($filters['codigo_autodenominacion']) && !empty($filters['codigo_autodenominacion']) ) {
                $query->where("persona.codigo_autodenominacion", '=', $filters['codigo_autodenominacion']);
            }
            
            if( isset($filters['codigo_grupo_poblacional']) && !empty($filters['codigo_grupo_poblacional']) ) {
                $query->where("persona.codigo_grupo_poblacional", '=', $filters['codigo_grupo_poblacional']);
            }
            
            if( isset($filters['codigo_grupo_comunitario']) && !empty($filters['codigo_grupo_comunitario']) ) {
                $query->where("persona.codigo_grupo_comunitario", '=', $filters['codigo_grupo_comunitario']);
            }
            
            if( isset($filters['codigo_grupo_social']) && !empty($filters['codigo_grupo_social']) ) {
                $query->where("persona.codigo_grupo_social", '=', $filters['codigo_grupo_social']);
            }
            
            if( isset($filters['discapacidad']) && !empty($filters['discapacidad']) ) {
                $query->where("persona.discapacidad", '=', $filters['discapacidad']);
            }

            $query->where('usuario.tipo_usuario', '=',  Usuario::typeUser('person'));
            $query->orderBy('usuario.nombre_usuario', 'asc');

            $usuarios = $query->get();

            // Prepare header
            $header = [];
            $header[] = 'Nombre';
            $header[] = 'Primer apellido';
            $header[] = 'Segundo apellido';
            $header[] = 'Género';
            $header[] = 'Ciudad de nacimiento';
            $header[] = 'Fecha de nacimiento';
            $header[] = 'Celular';
            $header[] = 'Nivel de educación';
            $header[] = 'Sector donde trabaja';
            $header[] = 'Tipo de documento';
            $header[] = 'Número de identificación';
            $header[] = 'Estado civil';
            $header[] = 'Ciudad de residencia';
            $header[] = 'Comuna/Corregimiento';
            $header[] = 'Barrio';
            $header[] = 'Estrato';
            $header[] = 'Tipo de vivienda';
            $header[] = 'Cabeza de familia o jefe de hogar';
            $header[] = 'Ocupación actual';
            $header[] = 'Tiene empresa o unidad productiva';
            $header[] = 'Sector donde trabaja';
            $header[] = 'Nivel de inglés';
            $header[] = 'Autodenominación';
            $header[] = 'Grupo poblacional';
            $header[] = 'Grupo comunitario';
            $header[] = 'Grupo social';
            $header[] = 'Discapacidad';

            //Prepare data
            $data = collect([]);
            foreach ($usuarios as $register)
            {
                unset($record);
                $record[] = $register->nombre_usuario;
                $record[] = $register->primer_apellido_persona;
                $record[] = $register->segundo_apellido_persona;
                $record[] = Usuario::getGender($register->genero_persona);
                $record[] = $register->nombre_ciudad;
                $record[] = $register->fecha_nacimiento_usuario;
                $record[] = $register->movil_usuario;
                $record[] = $register->nombre_nivel_educacion;
                $record[] = $register->nombre_sector_trabajo;
                $record[] = $register->nombre_tipo_identificacion;
                $record[] = $register->identificacion_usuario;
                $record[] = Usuario::getMaritalStatus($register->estado_civil_persona);
                $record[] = $register->ciudad_residencia;
                $record[] = $register->nombrelocalidad_ciudad;
                $record[] = $register->nombre_barrio_localidad;
                $record[] = $register->nombre_estrato;
                $record[] = $register->nombre_tipo_vivienda;
                $record[] = $register->jefe_hogar ? 'Sí' : 'No';
                $record[] = $register->nombre_lista_ocupacion;
                $record[] = $register->enterprise;
                $record[] = $register->nombre_sector_trabajo;
                $record[] = $register->nombre_nivel_idioma;
                $record[] = $register->nombre_autodenominacion;
                $record[] = $register->nombre_grupo_poblacional;
                $record[] = $register->nombre_grupo_comunitario;
                $record[] = $register->nombre_grupo_social;
                $record[] = $register->discapacidad ? 'Sí' : 'No';

                $data[] = $record;
            }
            
            // Prepare file
            $filename = sprintf('%s_%s.xlsx', 'usuarios', date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

            $file = Excel::create($filename, function($excel) use($header, $data) {
                $excel->sheet('Usuarios', function($sheet) use($header, $data) {
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
                throw new \Exception("Error recuperando xlsx reporte usuarios de dinamiza File($filepath).");
            }

            // Get url
            $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);

            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = "Reporte usuarios";
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution->codigo_institucion;
            $data['_url'] = $fileurl;
            SendEmail::notification($data);

        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'UsersDinamiza:command', $e->getMessage()));
            abort(500);
        }
    }
}
