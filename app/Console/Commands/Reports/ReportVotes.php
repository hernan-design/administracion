<?php

namespace App\Console\Commands\Reports;

use Illuminate\Console\Command;

use App\Models\Param\Institucion, App\Models\Seguridad\Usuario, App\Models\Core\Convocatoria, App\Models\Core\RegistroConvocatoria, App\Models\Core\Estado;
use App\Exports\ReportsExport;

use Log, Storage, Excel, DB, SendEmail;

class ReportVotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:votes {user} {institution} {program}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte votaciones emprendimientos convocatoria';

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

            $institution = Institucion::find($this->argument('institution'));
            if(!$institution instanceof Institucion) {
                throw new \Exception("Error recuperando institucion {$this->argument('institution')}.");
            }

            $program = Convocatoria::find($this->argument('program'));
            if(!$program instanceof Convocatoria){
                throw new \Exception("Error recuperando programa {$this->argument('program')}.");
            }

            $query = RegistroConvocatoria::query();
            $query->select('usuario.cargo_entidad', 'entidad.contador_like', 'numero_registro_convocatoria', 'usuario_institucion.contador_votos',
                DB::raw('initcap(nombre_ciudad) as nombre_ciudad'),
                DB::raw('initcap(distrito_ciudad) nombre_departamento'),
                DB::raw("initcap(usuario.nombre_usuario) as nombre_usuario"),
                DB::raw("initcap( (COALESCE(leader.nombre_usuario, '') || ' ' || COALESCE(pleader.primer_apellido_persona, '') || ' ' || COALESCE(pleader.segundo_apellido_persona, '')) ) as nombre_lider")
            );

            // Project
            $query->leftjoin('seguridad.usuario', 'registro_convocatoria.codigo_usuario', '=', 'usuario.codigo_usuario');
            $query->leftjoin('core.entidad', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');

            $query->join('core.usuario_institucion', function($join) use($institution){
                $join->on('entidad.codigo_usuario', '=', 'usuario_institucion.codigo_usuario')
                    ->where('usuario_institucion.codigo_institucion', '=', $institution->codigo_institucion);
            });

            // Leader
            $query->leftjoin('seguridad.usuario as leader', 'entidad.seg_codigo_usuario', '=', 'leader.codigo_usuario');
            $query->leftjoin('core.persona as pleader', 'leader.codigo_usuario', '=', 'pleader.codigo_usuario');

            // Location
			$query->leftjoin('core.ciudad', 'usuario.codigo_ciudad', '=', 'ciudad.codigo_ciudad');
            $query->leftjoin('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');

			$query->where('registro_convocatoria.codigo_convocatoria', $program->codigo_convocatoria);
			$query->where('registro_convocatoria.codigo_estado', Estado::getState('active'));
			$query->orderby('contador_like', 'desc');
			$query->orderby('numero_registro_convocatoria', 'asc');
        	$data = $query->get();

        	$header = [];
			$header[] = 'Registro convocatoria';
			$header[] = 'Proyecto';
			$header[] = 'Líder';
			$header[] = 'Departamento';
			$header[] = 'Municipio';
			$header[] = 'Institución educativa';
            $header[] = 'Número de like';
            $header[] = 'Número de votos';

			$datos = collect([]);
			foreach($data as $value){

				unset($record);
				$record[] = $value->numero_registro_convocatoria;
				$record[] = $value->nombre_usuario;
				$record[] = $value->nombre_lider;
				$record[] = $value->nombre_departamento;
				$record[] = $value->nombre_ciudad;
				$record[] = $value->cargo_entidad;
                $record[] = $value->contador_like;
                $record[] = $value->contador_votos;

				$datos[] = $record;
			}

            // Prepare file
            $filename = sprintf('%s_%s_%s.xlsx', 'votos', $program->url_convocatoria, date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);

            $export = new ReportsExport([$header], [$datos]);

            // Genera el archivo Excel
            $file = Excel::store($export, $filepath, 's3', null, [
              'visibility' => 'public',
            ]);

            $fileUrl = Storage::disk('s3')->url($filepath);

            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = "Reporte votos $program->nombre_convocatoria";
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution->codigo_institucion;
            $data['_url'] = $fileUrl;
            SendEmail::notification($data);

        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReportVotes:Command', $e->getMessage()));
            abort(500);
        }
    }
}
