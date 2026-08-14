<?php

namespace App\Console\Commands\Reports;

use App\Exports\ReportsExport;
use Illuminate\Console\Command;
use App\Models\Core\Estado,
    App\Models\Seguridad\Usuario,
    App\Models\Param\Institucion,
    App\Models\Core\Entidad,
    App\Models\Core\TipoSociedad;
use Log, Storage, Excel, DB, SendEmail;

class ReportEntities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:entities {user} {institution}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte de sesiones de mentoria para toda la app';

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

            \Log::info("Inicia Consulta \n");
            //
            $query = Usuario::query();
            $query->select(
                'usuario.codigo_usuario',
                'usuario.codigo_estado',
                'usuario.identificacion_usuario',
               //'usuario.nombre_usuario',
                'usuario.codigo_ciudad',
                'usuario.telefono_usuario',
                'usuario.biografia_usuario',
                'usuario.twitter_usuario',
                'usuario.facebook_usuario',
                'usuario.instagram_usuario',
                'usuario.linkedin_usuario',
                'usuario.numero_whatsapp',
                'usuario.pagina_web_usuario',
                'usuario.fecha_nacimiento_usuario',
                'entidad.codigo_clase_empre_insti',
                'entidad.codigo_tipo_sociedad',
                'entidad.codigo_sector_interes',
                'entidad.cubrimiento_entidad',
                'entidad.codigo_tipo_identificacion',
                'entidad.correo_contacto_entidad',
                'clase_emprendimiento.nombre_clase_emprendimiento',
                'tipo_identificacion.nombre_tipo_identificacion',
                'ciudad.nombre_ciudad',
                'sector_interes.nombre_sector_interes',
                DB::raw("initcap(CASE WHEN usuario.tipo_usuario = ".Usuario::typeUser('person').
                    " THEN ( (COALESCE(usuario.nombre_usuario, '') || ' '
                    || COALESCE(persona.primer_apellido_persona, '') || ' '
                    || COALESCE(persona.segundo_apellido_persona, '')) )
                    ELSE usuario.nombre_usuario END) AS nombre_usuario"),
                'founder.codigo_usuario as codigo_founder',
                'founder.nickname_usuario as nickname_founder',
                'founder.correo_electronico as correo_founder',
                DB::raw('initcap(etapa_proyecto.nombre_etapa_proyecto) as nombre_etapa_proyecto'),
                DB::raw('initcap(categoria_proyecto.nombre_categoria_proyecto) as nombre_categoria_proyecto'),
                DB::raw("trim(initcap(ciudadf.nombre_ciudad)) as ciudad_founder"),
                DB::raw("COALESCE(initcap(ciudadf.distrito_ciudad), '') as distrito_ciudad_founder"),
                'paisf.nombre_pais as pais_founder',
                DB::raw("initcap(CASE WHEN founder.tipo_usuario = ".Usuario::typeUser('person')."
                    THEN (trim(founder.nombre_usuario) || ' ' || trim(personaf.primer_apellido_persona)
                    || ' ' || trim(personaf.segundo_apellido_persona)) ELSE founder.nombre_usuario END)
                    as nombre_founder")
            );

            $query->join('core.usuario_institucion', function($join) use($institution){
                $join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
                    //->whereNotIn('usuario_institucion.codigo_estado', [Estado::getState('inactive'), Estado::getState('removed')])
                    ->where('usuario_institucion.codigo_institucion', '=', $institution->codigo_institucion);
            });

            $query->leftJoin('core.persona', 'usuario.codigo_usuario', '=', 'persona.codigo_usuario');

            $query->join('core.entidad', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');

            // Founder City / Country
            $query->join('seguridad.usuario as founder', 'entidad.seg_codigo_usuario', '=', 'founder.codigo_usuario');
            $query->leftjoin('core.ciudad as ciudadf', 'founder.codigo_ciudad', '=', 'ciudadf.codigo_ciudad');
            $query->leftjoin('core.pais as paisf', 'ciudadf.codigo_pais', '=', 'paisf.codigo_pais');
            $query->leftjoin('core.persona as personaf', 'personaf.codigo_usuario', '=', 'founder.codigo_usuario');

            $query->join('core.dato_entidad', 'usuario_institucion.codigo_usuario_institucion', '=', 'dato_entidad.codigo_usuario_institucion');
            $query->leftjoin('core.categoria_proyecto', 'dato_entidad.codigo_categoria_proyecto', '=', 'categoria_proyecto.codigo_categoria_proyecto');
            $query->leftjoin('core.etapa_proyecto', 'dato_entidad.codigo_etapa_proyecto', '=', 'etapa_proyecto.codigo_etapa_proyecto');

            $query->leftjoin('core.clase_emprendimiento_insti as ce', 'ce.codigo_clase_empre_insti', '=', 'entidad.codigo_clase_empre_insti');
            $query->leftjoin('core.clase_emprendimiento', 'clase_emprendimiento.codigo_clase_emprendimiento', '=', 'ce.codigo_clase_emprendimiento');

            $query->leftjoin('core.tipo_identificacion', 'tipo_identificacion.codigo_tipo_identificacion', '=', 'entidad.codigo_tipo_identificacion');

            $query->leftjoin('core.sector_interes', 'sector_interes.codigo_sector_interes', '=', 'entidad.codigo_sector_interes');

            $query->leftjoin('core.ciudad', 'ciudad.codigo_ciudad', '=', 'usuario.codigo_ciudad');

            $query->whereIn('usuario.codigo_estado', [Estado::getState('active'), Estado::getState('inactive')]);

            $query->orderBy('nombre_usuario', 'asc');

            $entities = $query->get();

            // Prepare header
            $header = [];
            $header[] = 'Nombre creador';
            $header[] = 'Correo creador';
            $header[] = 'Tipo Entidad';
            $header[] = 'Tipo identificación';
            $header[] = 'Número de identificación';
            $header[] = 'Nombre';
            $header[] = 'Ciudad';
            $header[] = 'Teléfono';
            $header[] = 'Número whatsapp';
            $header[] = 'Fecha inicio';
            $header[] = transtp('entities.report.microsector', [], $institution->slug_institucion);
            $header[] = transtp('entities.report.macrosector', [], $institution->slug_institucion);
            $header[] = 'Actividad económica';
            $header[] = transtp('entities.report.tiposociedad', [], $institution->slug_institucion);
            $header[] = transtp('entities.report.cubrimiento', [], $institution->slug_institucion);
            $header[] = 'Correo contácto';
            $header[] = 'Descripción empresa/proyecto';
            $header[] = 'Facebook';
            $header[] = 'Twitter';
            $header[] = 'Instagram';
            $header[] = 'LinkedIn';
            $header[] = 'Página web';
            $header[] = 'Estado';

             //Prepare data
            $data = collect([]);
            foreach ($entities as $entitie)
            {
                unset($record);
                $record[] = $entitie->nombre_founder;
                $record[] = $entitie->correo_founder;
                $record[] = $entitie->nombre_clase_emprendimiento;
                $record[] = $entitie->nombre_tipo_identificacion;
                $record[] = $entitie->identificacion_usuario;
                $record[] = $entitie->nombre_usuario;
                $record[] = $entitie->nombre_ciudad;
                $record[] = $entitie->telefono_usuario;
                $record[] = $entitie->numero_whatsapp;
                $record[] = $entitie->fecha_nacimiento_usuario;
                $record[] = $entitie->nombre_categoria_proyecto;
                $record[] = $entitie->nombre_etapa_proyecto;
                $record[] = $entitie->nombre_sector_interes;
                $record[] = is_numeric($entitie->codigo_tipo_sociedad)
                                ? $this->getTypeSociety($entitie->codigo_tipo_sociedad)
                                : '';
                $record[] = is_numeric($entitie->cubrimiento_entidad)
                                ? Entidad::$cover[$entitie->cubrimiento_entidad]
                                : '';
                $record[] = $entitie->correo_contacto_entidad;
                $record[] = strip_tags($entitie->biografia_usuario);;
                $record[] = $entitie->facebook_usuario;
                $record[] = $entitie->twitter_usuario;
                $record[] = $entitie->instagram_usuario;
                $record[] = $entitie->linkedin_usuario;
                $record[] = $entitie->pagina_web_usuario;
                $record[] = Estado::getNameState($entitie->codigo_estado);

                $data[] = $record;
            }

            \Log::info("Fin Consulta \n");
            // Prepare file

            $filename = sprintf('%s_%s_%s.xlsx', 'Reporte de entidades de ', $institution->nombre_institucion, date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', $institution->codigo_institucion, $user->codigo_usuario, $filename);
            $inst_color = $institution->color();

            $export = new ReportsExport([$header], [$data]);

            // Genera el archivo Excel
            $file = Excel::store($export, $filepath, 's3', null, [
              'visibility' => 'public',
            ]);

            $fileUrl = Storage::disk('s3')->url($filepath);
            \Log::info("Creo excel \n");

            \Log::info("Cargo excel \n");
            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = "Reporte entidades $institution->nombre_institucion";
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution->codigo_institucion;
            $data['_url'] = $fileUrl;
            SendEmail::notification($data);

            \Log::info("Envio correo \n");

        }catch(\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReportEntities:command', $e->getMessage()));
            abort(500);
        }
    }

    /**
     * @return string with name nombre_tipo_sociedad
     * @param int $codigo_sector_interes
     */
    public function getTypeSociety(int $codigo_tipo_sociedad): string
	{
		$query = TipoSociedad::query();
        $query->select('nombre_tipo_sociedad');
		$query->where('codigo_estado', '=', Estado::getState('active'));
		$query->where('codigo_tipo_sociedad', '=', $codigo_tipo_sociedad);
        $query = $query->get()->first();
        return isset($query) ? $query->nombre_tipo_sociedad : '';
	}
}
