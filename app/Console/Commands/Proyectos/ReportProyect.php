<?php

namespace App\Console\Commands\Proyectos;

use Illuminate\Console\Command;

use App\Models\Seguridad\Usuario, App\Models\Core\Estado;

use DB, Excel, Storage, SendEmail;

class ReportProyect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:proyect {user}{institution}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Report To Proyects';

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
            $institution = $this->argument('institution');
            $user = Usuario::find($this->argument('user'));
			if(!$user instanceof Usuario) {
				throw new \Exception("Error recuperando usuario {$this->argument('user')}.");
			}

            $query = Usuario::query();
            $query->select('usuario.codigo_usuario', 'usuario.nickname_usuario', 'usuario.codigo_estado', 'founder.identificacion_usuario',
                'founder.movil_usuario', 'founder.correo_electronico', 'founder.fecha_nacimiento_usuario', 'entidad.descripcion_entidad',
                'entidad.imagen_mini_entidad', 'entidad.video_entidad', 'entidad.facebook_entidad', 'entidad.twitter_entidad',
                'entidad.linkedin_entidad', 'entidad.instagram_entidad', 'privacidad_contenido.nombre_privacidad_contenido',
                DB::raw("initcap(CASE WHEN usuario.tipo_usuario = ".Usuario::typeUser('person')." THEN ( (COALESCE(usuario.nombre_usuario, '') || ' ' || COALESCE(persona.primer_apellido_persona, '') || ' ' || COALESCE(persona.segundo_apellido_persona, '')) ) ELSE usuario.nombre_usuario END) AS emprendimiento"),
                DB::raw("COALESCE(initcap(founder.nombre_usuario), '') as nombre_usuario"),
                DB::raw("COALESCE(initcap(personaf.primer_apellido_persona), '') as primer_apellido"),
                DB::raw("COALESCE(initcap(personaf.segundo_apellido_persona), '') as segundo_apellido"),
                DB::raw('initcap(etapa_proyecto.nombre_etapa_proyecto) as nombre_etapa_proyecto'),
                DB::raw('initcap(categoria_proyecto.nombre_categoria_proyecto) as nombre_categoria_proyecto'),
                DB::raw("trim(initcap(ciudadf.nombre_ciudad)) as ciudad_founder")
                // DB::raw("COALESCE(initcap(ciudadf.distrito_ciudad), '') as distrito_ciudad_founder"), 'paisf.nombre_pais as pais_founder',
                // DB::raw("initcap(CASE WHEN founder.tipo_usuario = ".Usuario::typeUser('person')." THEN (trim(founder.nombre_usuario) || ' ' || trim(personaf.primer_apellido_persona) || ' ' || trim(personaf.segundo_apellido_persona)) ELSE founder.nombre_usuario END) as nombre_founder")
            );

            $query->join('core.usuario_institucion', function($join) use($institution) {
                $join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
                    ->whereNotIn('usuario_institucion.codigo_estado', [Estado::getState('inactive'), Estado::getState('removed')])
                    ->where('usuario_institucion.codigo_institucion', '=', $institution);
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
            $query->leftjoin('core.privacidad_contenido', 'privacidad_contenido.codigo_privacidad_contenido', '=', 'dato_entidad.codigo_privacidad_contenido');

            $query->whereIn('usuario.codigo_estado', [Estado::getState('active'), Estado::getState('inactive')]);

            $query->distinct();
            $query->orderBy('nombre_usuario', 'asc');
            $entities = $query->get();

            // Prepare header
            $header = [];
            $header[] = 'Identificacion Usuario';
            $header[] = 'Nombre Usuario';
            $header[] = 'Primer Apellido';
            $header[] = 'Segundo Apellido';
            $header[] = 'Movil Usuario';
            $header[] = 'Correo Electronico';
            $header[] = 'Emprendimiento';
            $header[] = 'Nombre Etapa Proyecto';
            $header[] = 'Nombre Categoria Proyecto';
            $header[] = 'Fecha Inicio';
            $header[] = 'Nombre Privacidad Contenido';
            $header[] = 'Descripcion';
            $header[] = 'Nombre Ciudad';
            $header[] = 'Imagen Proyecto';
            $header[] = 'Video Entidad';
            $header[] = 'Facebook Usuario';
            $header[] = 'Twitter Usuario';
            $header[] = 'Linkedin Usuario';
            $header[] = 'Instagram Usuario';

            //Prepare data
            $data = collect([]);
            foreach ($entities as $register)
            {
                unset($record);
                $record[] = $register->identificacion_usuario;
                $record[] = $register->nombre_usuario;
                $record[] = $register->primer_apellido;
                $record[] = $register->segundo_apellido;
                $record[] = $register->movil_usuario;
                $record[] = $register->correo_electronico;
                $record[] = $register->emprendimiento;
                $record[] = $register->nombre_etapa_proyecto;
                $record[] = $register->nombre_categoria_proyecto;
                $record[] = $register->fecha_nacimiento_usuario;
                $record[] = $register->nombre_privacidad_contenido;
                $record[] = $register->descripcion_entidad;
                $record[] = $register->ciudad_founder;
                $record[] = $register->imagen_mini_entidad;
                $record[] = $register->video_entidad;
                $record[] = $register->facebook_entidad;
                $record[] = $register->twitter_entidad;
                $record[] = $register->linkedin_entidad;
                $record[] = $register->instagram_entidad;

                $data[] = $record;
            }

            // Prepare file
            $filename = sprintf('%s_%s.xlsx', 'reporte_proyectos', date('YmdHms'));
            $filepath = sprintf('reports/%s/%s/%s', $institution, $user->codigo_usuario, $filename);

            $file = Excel::create($filename, function($excel) use($header, $data) {
                // Set the title
                $excel->setTitle('Reporte Proyectos');

                // Set sheet alignement
                $excel->getDefaultStyle()
                        ->getAlignment()
                        ->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $excel->sheet('Reporte Proyectos', function($sheet) use($header, $data) {

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
                    $sheet->prependRow(1,['Proyectos']);

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
                throw new \Exception("Error recuperando xlsx reporte cursos File($filepath).");
            }

            // Get url
            $fileurl = \AWS::createClient('s3')->getObjectUrl(config('filesystems.disks.s3.bucket'), $filepath);

            // Send mail
            $data = [];
            $data['_template'] = 'emails.report';
            $data['_subject'] = 'Reporte Proyectos';
            $data['_to'] = $user->correo_electronico;
            $data['_to_name'] = ucwords(strtolower($user->nombre_usuario));
            $data['_inst'] = $institution;
            $data['_url'] = $fileurl;
            SendEmail::notification($data);

        } catch (\Exception $e) {
            \Log::error(sprintf('%s:%s', 'ReportProyect:Command', $e->getMessage()));
			abort(500);
        }
    }
}
