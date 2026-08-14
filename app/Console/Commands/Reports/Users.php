<?php

namespace App\Console\Commands\Reports;

use Illuminate\Console\Command;
use App\Exports\ReportsExport;

use Log, Storage, Excel, DB, SendEmail;

use App\Models\Seguridad\Usuario, App\Models\Param\Institucion, App\Models\Core\Estado, App\Models\Seguridad\Rol;

class Users extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:users {user} {institution}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte usuarios inscritos a la institución en xlsx';

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
            $query->select('usuario.codigo_usuario',  'usuario.identificacion_usuario', 'tipo_identificacion.nombre_tipo_identificacion', 'genero_persona', 'nombnre_profesion_persona', 'nombre_ocupacion_persona', 'fecha_registro_info_basica',
                DB::raw('initcap(nombre_pais) AS nombre_pais'),
                DB::raw('initcap(nombre_ciudad) AS nombre_ciudad'),
                DB::raw('initcap(distrito_ciudad) AS nombre_departamento'),
                DB::raw("initcap(usuario.nombre_usuario) AS nombre_usuario"),
                DB::raw('lower(usuario.correo_electronico) AS correo_electronico'),
                DB::raw('EXTRACT(YEAR FROM age(fecha_nacimiento_usuario)) AS fecha_nacimiento_usuario'),
                DB::raw("(CASE WHEN rol_usuario.role_id IS NOT NULL THEN 'Si' ELSE 'No' END) AS asociado"),
                DB::raw("initcap(COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) AS apellido_usuario"),
                DB::raw("(
                        SELECT STRING_AGG(canal.nombre_canal::text, ',') FROM core.canal
                        JOIN core.canal_usuario ON canal.codigo_canal = canal_usuario.codigo_canal
                        WHERE usuario_institucion.codigo_usuario_institucion = canal_usuario.codigo_usuario_institucion
                    ) AS nombre_canal")
            );

            $query->join('core.persona','usuario.codigo_usuario','=','persona.codigo_usuario');
            $query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
            $query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

            //  User institution
            $query->join('core.usuario_institucion', function($join) use($institution){
                $join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
                    ->where('usuario_institucion.codigo_institucion', '=', $institution->codigo_institucion)
                    ->whereNotIn('usuario_institucion.codigo_estado', [Estado::getState('inactive'), Estado::getState('removed')]);
            });

            $query->leftJoin('seguridad.rol_usuario', function($join) {
				$join->on('rol_usuario.codigo_institucion', '=', 'usuario_institucion.codigo_institucion')
					->on('rol_usuario.user_id', '=', 'usuario_institucion.codigo_usuario')
					->where('rol_usuario.role_id', '=', Rol::getRol('associated') );
            });

            $query->leftJoin('core.tipo_identificacion', 'tipo_identificacion.codigo_tipo_identificacion', '=', 'usuario.tipo_identificacion_usuario');

            $query->leftJoin('core.ocupacion_persona', function($join){
                $join->on('persona.codigo_ocupacion_persona', '=', 'ocupacion_persona.codigo_ocupacion_persona')
                    ->where('ocupacion_persona.codigo_estado', '=', Estado::getState('active'));
            });

            $query->leftJoin('core.profesion_persona', function($join){
                $join->on('persona.codigo_profesion_persona', '=', 'profesion_persona.codigo_profesion_persona')
                    ->where('profesion_persona.codigo_estado', '=', Estado::getState('active'));
            });

            $query->where('usuario.tipo_usuario', '=',  Usuario::typeUser('person'));
            $query->orderBy('usuario.nombre_usuario', 'asc');

            $usuarios = $query->get();

            // Prepare header
            $header = [];
            $header[] = 'Correo electrónico';
            $header[] = 'Nombres';
            $header[] = 'Apellido';
			$header[] = 'Tipo identificación';
			$header[] = 'Número identificación';
            $header[] = 'Edad';
            $header[] = 'Género';
            $header[] = 'País';
            $header[] = 'Departamento';
            $header[] = 'Ciudad';
            $header[] = 'Usuario asociado';
            $header[] = 'Profesión';
            $header[] = 'Ocupación';
            $header[] = 'Fecha de registro';
            $header[] = 'Círculo de profesionales';

            //Prepare data
            $data = collect([]);
            foreach ($usuarios as $register)
            {
                unset($record);
                $record[] = $register->correo_electronico;
                $record[] = $register->nombre_usuario;
                $record[] = $register->apellido_usuario;
				$record[] = $register->nombre_tipo_identificacion;
				$record[] = $register->identificacion_usuario;
                $record[] = $register->fecha_nacimiento_usuario;
                $record[] = Usuario::getGender($register->genero_persona);
                $record[] = $register->nombre_pais;
                $record[] = $register->nombre_departamento;
                $record[] = $register->nombre_ciudad;
                $record[] = $register->asociado;
                $record[] = $register->nombnre_profesion_persona;
                $record[] = $register->nombre_ocupacion_persona;
                $record[] = $register->fecha_registro_info_basica;
                $record[] = $register->nombre_canal;

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
            Log::error(sprintf('%s:%s', 'Users:command', $e->getMessage()));
            abort(500);
        }
    }
}
