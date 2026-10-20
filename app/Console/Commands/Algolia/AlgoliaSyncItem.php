<?php
namespace App\Console\Commands\Algolia;
use Illuminate\Console\Command;
use URL, DB, Log;
use App\Models\Core\Contenido,App\Models\Core\Convocatoria, App\Models\Core\Estado, App\Models\Core\Foro,
  App\Models\Aprende\Curso, App\Models\Core\Producto, App\Models\Core\Evento, App\Models\Seguridad\Usuario,
  App\Models\Core\PrivacidadContenido, App\Models\Seguridad\Rol, App\Models\Param\TerminosCondiciones,  App\Models\Param\Institucion;

  class AlgoliaSyncItem extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'algolia:syncitem 
                            {type : Type of item} 
                            {id : Id of item}
                            {--F|force : Force sync to algolia ignoring config algolia_sync parameter value}
                            {--D|dev : Print to LOG and sync objects}
                            {--L|log : Print to LOG  but don\'t sync objects}';
  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Sync content for index in Algolia for an item';
  private $index = null;
  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->remote_sync  = config()->has('tuproyecto.algolia_sync') ?  config('tuproyecto.algolia_sync') : true;
    $this->client = \Algolia\AlgoliaSearch\Api\SearchClient::create(config('algolia.id'), config('algolia.admin_api_key'));
    parent::__construct();
  }
  /**
   * Execute the console command.
   *
   * @return mixed
   * Sync_algolia (0 - Sincronizado, 1 - Por sincronizar, 2 - No sincronizable, 3 - Eliminar indice)
   */
  public function handle()
  {

    $type = $this->argument('type');
    $id = $this->argument('id');
   
    if($this->option('dev')){ 
      $this->info('Algolia Sync is on dev mode!');
      Log::warning('Algolia Sync is on dev mode!');
    }elseif($this->option('log')) {
      $this->info('Algolia Sync is on log mode!');
      Log::warning('Algolia Sync is on log mode!');
    }
    elseif(!$this->remote_sync &&  !$this->option('force')) {
      $this->info('Algolia Sync is off!');   
      Log::warning('Algolia Sync is off!');
    }

    if(is_numeric($id)) {
      switch ($type) {
        case 'article':
            //$this->indexBlog($id);
            break;

        case 'forum':
            //$this->indexForum($id);
            break;
        
        case 'course':
            //$this->indexCourse($id);
            break;
        
        case 'product':
            //$this->indexProduct($id);
            break;

        case 'event':
            //$this->indexEvent($id);
            break;    

        case 'mentor':
            //$this->indexMentor($id);
            break;

        case 'program':
            //$this->indexProgram($id);
            break;                  
      }
      Log::info('Algolia Sync '.$type.'-id='.$id.' completed!');
      $this->info('Algolia Sync '.$type.'-id='.$id.' completed!');
    }else{
      Log::info('Algolia Desync was aborted because id '.$id.' is invalid for '.$type);
      $this->info('Algolia Desync was aborted because id '.$id.' is invalid for '.$type); 
    } 

  }

  public function indexBlog($id)
  {
   
    // Sync content blog
    $query = Contenido::query();
    $query->select('sync_algolia', DB::raw('contenido.codigo_contenido as id'), DB::raw('contenido.nombre_contenido as title'), DB::raw('contenido.imagen_contenido as image'), DB::raw('contenido.resumen_contenido as description'), DB::raw('contenido.url_contenido as url'), DB::raw('contenido.contador_like as likes'), DB::raw('contenido.contador_comentario as comments'),

      DB::raw("(SELECT string_agg(ci.codigo_institucion::text, ',') FROM core.contenido_institucion as ci 
            WHERE ci.codigo_contenido = contenido.codigo_contenido AND ci.codigo_estado = ".Estado::getState('active').") as instituciones"),

      DB::raw("(SELECT string_agg(trim(t.texto_tag), ',') FROM core.tag_contenido as tc join core.tag as t ON tc.codigo_tag = t.codigo_tag
                WHERE tc.codigo_contenido = contenido.codigo_contenido) as tags"),

      DB::raw("(SELECT string_agg(cic.codigo_institucion || ',' || url_tipo_contenido, ';')
                FROM core.contenido_institucion as cic
                JOIN param.tipo_contenido_insti ON cic.codigo_tipo_cont_institucion = tipo_contenido_insti.codigo_tipo_cont_institucion 
                JOIN core.tipo_contenido ON tipo_contenido_insti.codigo_tipo_contenido = tipo_contenido.codigo_tipo_contenido 
                WHERE
                cic.codigo_contenido = contenido.codigo_contenido AND cic.codigo_estado = ".Estado::getState('active').") as categorias"
      )
    );
    $query->whereNotIn('contenido.imagen_contenido', [
      'https://lh6.googleusercontent.com/-NtJLiTZeioY/Uk8mE2BraXI/AAAAAAAABlc/0J7qX7azPUo/w370-h206-c/sinimagenproducto.png'
    ]);
    $query->where('contenido.codigo_estado', Estado::getState('active'));
    $query->whereIn('contenido.sync_algolia', [1,3]);
    $query->where('codigo_contenido', $id);
    $article = $query->first();
    if($article) {
      $this->info('Synchronizing index for article id '.$id);
      Log::info('Synchronizing index for article id '.$id);
      if($this->sycn('article', 'blog.article', $article)){
        // Update state sync algolia
        $articleID = $article->id;
        Contenido::where('codigo_contenido', $articleID)->where('codigo_estado', Estado::getState('active'))->where('sync_algolia', 1)->update(['sync_algolia' => 0]);
        Contenido::where('codigo_contenido', $articleID)->where('codigo_estado', Estado::getState('active'))->where('sync_algolia', 3)->update(['sync_algolia' => 2]);
      }
      $this->info('Article index was successfully synchronized!');
      Log::info('Article index was successfully synchronized!');
    }
  }

  public function indexForum($id)
  {
   
    // Sync content forum
    $query = Foro::query();
    $query->select('sync_algolia', DB::raw('foro.codigo_foro as id'), DB::raw('foro.titulo_foro as title'), DB::raw("'".config('tuproyecto.image_forum')."' as image"), DB::raw('foro.descripcion_foro as description'), DB::raw('foro.url_foro as url'), DB::raw('categoria_foro.url_categoria_foro as url_category'), DB::raw('foro.contador_like as likes'), DB::raw('foro.contador_comentario as comments'),
      DB::raw("(SELECT string_agg(cf.codigo_institucion::text, ',') FROM core.foro_institucion as cf 
                WHERE cf.codigo_foro = foro.codigo_foro AND cf.codigo_estado = ".Estado::getState('active').") as instituciones"),
      DB::raw("(SELECT string_agg(trim(t.texto_tag), ',') FROM core.tag_foro as tf join core.tag as t ON tf.codigo_tag = t.codigo_tag
                WHERE tf.codigo_foro = foro.codigo_foro) as tags")
    );
    $query->join('core.categoria_foro', 'foro.codigo_categoria_foro', '=', 'categoria_foro.codigo_categoria_foro');
    $query->where('foro.codigo_estado', Estado::getState('active'));
    $query->where('foro.foro_grupo', false);
    $query->whereIn('foro.sync_algolia', [1,3]);
    $query->where('codigo_foro', $id);
    $forum = $query->first();
    if($forum) {
      $this->info('Synchronizing index for forum id '.$id);
      Log::info('Synchronizing index for forum id '.$id);
      if($this->sycn('forum', 'forum.forum', $forum)){
        // Update state sync algolia
        $forumID = $forum->id;
        Foro::where('codigo_foro', $forumID)->where('codigo_estado', Estado::getState('active'))->where('foro_grupo', false)->where('sync_algolia', 1)->update(['sync_algolia' => 0]);
        Foro::where('codigo_foro', $forumID)->where('codigo_estado', Estado::getState('active'))->where('foro_grupo', false)->where('sync_algolia', 3)->update(['sync_algolia' => 2]);
      }     
      $this->info('Forums were successfully synchronized!');
      Log::info('Forums were successfully synchronized!');
    }
  }

  public function indexCourse($id)
  {
    // Sync content course
    $query = Curso::query();
    $query->select('sync_algolia', DB::raw('curso.codigo_curso as id'), DB::raw('curso.nombre_curso as title'), DB::raw('curso.imagen_curso as image'), DB::raw('curso.descripcion_curso as description'), DB::raw('curso.url_curso as url'), DB::raw('categoria_curso.url_categoria_curso as url_category'), DB::raw('ci.contador_inscritos as participants'),
      DB::raw("(SELECT string_agg(ci.codigo_institucion::text, ',') FROM aprende.curso_institucion as ci 
                WHERE ci.codigo_curso = curso.codigo_curso AND ci.codigo_estado = ".Estado::getState('active').") as instituciones")
    );
    $query->join('aprende.categoria_curso', 'curso.codigo_categoria_curso', '=', 'categoria_curso.codigo_categoria_curso');
    $query->join('aprende.curso_institucion as ci', 'ci.codigo_curso', '=', 'curso.codigo_curso');
    $query->where('curso.codigo_privacidad_contenido', '=', PrivacidadContenido::getPrivacy('public'));
    $query->where('curso.codigo_estado', Estado::getState('active'));
    $query->whereIn('curso.sync_algolia', [1,3]);
    $query->where('curso.codigo_curso', $id);

    $course = $query->first();
    if($course) {
        $this->info('Synchronizing index for course id '.$id);
        Log::info('Synchronizing index for course id '.$id); 
        if($this->sycn('course', 'learn.course', $course)){
            $courseID = $course->id;
            // Update state sync algolia
            Curso::where('codigo_curso', $courseID)->where('codigo_estado', Estado::getState('active'))->where('sync_algolia', 1)->update(['sync_algolia' => 0]);
            Curso::where('codigo_curso', $courseID)->where('codigo_estado', Estado::getState('active'))->where('sync_algolia', 3)->update(['sync_algolia' => 2]);
        }
        $this->info('Course index was successfully synchronized!');
        Log::info('Course index was successfully synchronized!'); 
    }
  }

  public function indexProduct($id)
  {
    
    // Sync content product
    $query = Producto::query();
    $query->select('producto.sync_algolia',
      DB::raw('producto.codigo_producto as id'),
      DB::raw('producto.nombre_producto as title'),
      DB::raw('producto.imagen_producto as image'),
      DB::raw('producto.resumen_producto as description'),
      DB::raw('producto.precio_producto as precio'),
      DB::raw('upper( concat( trim(ue.nombre_usuario), \' \', trim(persona.primer_apellido_persona) ) )  as entity_name'),
      DB::raw('ue.codigo_usuario as entity'),
      DB::raw('producto.url_producto as url'),
      DB::raw('producto.contador_like as likes'),
      DB::raw('producto.contador_comentario as comments'),
      DB::raw("(SELECT string_agg(pdi.codigo_institucion::text, ',') FROM core.producto as pdi
				WHERE pdi.codigo_producto = producto.codigo_producto AND pdi.codigo_estado = ".Estado::getState('active') . ") as instituciones"),
      DB::raw("(SELECT string_agg(cat.nombre_categoria_producto, ',') FROM core.categoria_producto as cat
            	JOIN core.categoria_producto_insti ON  cat.codigo_categoria_producto = categoria_producto_insti.codigo_categoria_producto 
				WHERE  categoria_producto_insti.codigo_categoria_prod_insti = producto.codigo_categoria_prod_insti
            	AND categoria_producto_insti.codigo_estado = ".Estado::getState('active') . ") as categorias"),
      DB::raw("(SELECT string_agg(cat.codigo_categoria_producto::TEXT, ',') FROM core.categoria_producto as cat
            	JOIN core.categoria_producto_insti ON  cat.codigo_categoria_producto = categoria_producto_insti.codigo_categoria_producto 
				WHERE  categoria_producto_insti.codigo_categoria_prod_insti = producto.codigo_categoria_prod_insti
            	AND categoria_producto_insti.codigo_estado = ".Estado::getState('active') . ") as idcategorias"),
      DB::raw("(SELECT string_agg(trim(t.texto_tag), ',') FROM core.producto_tag as tp join core.tag as t ON tp.codigo_tag = t.codigo_tag
				WHERE tp.codigo_producto = producto.codigo_producto AND tp.codigo_estado = ". Estado::getState('active'). ") as tags")
    );
    $query->leftjoin('seguridad.usuario as ue', 'ue.codigo_usuario', '=', 'producto.seg_codigo_usuario');
    $query->leftjoin('core.persona', 'persona.codigo_usuario', '=', 'ue.codigo_usuario');
    $query->where('producto.codigo_estado', Estado::getState('active'));
    $query->whereIn('producto.sync_algolia', [1,3]);
    $query->where('codigo_producto', $id);
      
    $product = $query->first();
    if($product) {
      $this->info('Synchronizing index for product id '.$id);
      Log::info('Synchronizing index for product id '.$id); 
      if($this->sycn('product', 'product.product', $product)){
        // Update state sync algolia
        $productID = $product->id;
        Producto::where('codigo_producto', $productID)->where('codigo_estado', Estado::getState('active'))->where('sync_algolia', 1)->update(['sync_algolia' => 0] );
        Producto::where('codigo_producto', $productID)->where('codigo_estado', Estado::getState('active'))->where('sync_algolia', 3)->update(['sync_algolia' => 2]);
      }
      $this->info('Product index was successfully synchronized!');
      Log::info('Product index was successfully synchronized!');
    }
  }
  public function indexEvent($id)
  {
    // Sync content event
    $query = Evento::query();
    $query->select('evento.sync_algolia', DB::raw('evento.codigo_evento as id'),
      DB::raw('evento.nombre_evento as title'), DB::raw('evento.imagen_evento as image'),
      DB::raw('evento.fecha_fin_evento as deadline'),
      DB::raw("initcap( concat( trim(ciudad.nombre_ciudad), ', ', trim(ciudad.distrito_ciudad) ) ) as location"),
      DB::raw('evento.resumen_evento as description'), DB::raw('evento.url_evento as url'),
      DB::raw('tipo_evento.url_tipo_evento as url_category'),
      DB::raw('evento_institucion.contador_like as likes'),
      DB::raw('evento_institucion.contador_comentario as comments'),
      DB::raw("(SELECT pa.codigo_patrocinador FROM aprende.patrocinador as pa
					JOIN core.organizador_evento  ON organizador_evento.codigo_patrocinador = pa.codigo_patrocinador
            		WHERE organizador_evento.codigo_evento = evento.codigo_evento 
            		AND organizador_evento.codigo_estado = ".Estado::getState('active') . " 
            		LIMIT 1) as entity"),
      DB::raw("(SELECT upper(trim(pa.nombre_patrocinador)) FROM aprende.patrocinador as pa
					JOIN core.organizador_evento  ON organizador_evento.codigo_patrocinador = pa.codigo_patrocinador
            		WHERE organizador_evento.codigo_evento = evento.codigo_evento 
            		AND organizador_evento.codigo_estado = ".Estado::getState('active') . " 
            		LIMIT 1) as entity_name"),
      DB::raw("(SELECT string_agg(evi.codigo_institucion::text, ',') FROM core.evento_institucion as evi
            	WHERE evi.codigo_evento = evento.codigo_evento AND evi.codigo_estado = ".Estado::getState('active') . ") as instituciones"),
      DB::raw("(SELECT string_agg(ec.nombre_etapa_contenido, ',') FROM core.etapa_contenido as ec
            	JOIN core.etapa_cont_event ON etapa_cont_event.codigo_etapa_contenido = ec.codigo_etapa_contenido
            	WHERE  etapa_cont_event.codigo_evento = evento.codigo_evento
            	AND etapa_cont_event.codigo_estado = ".Estado::getState('active') . ") as etapas"),
      DB::raw("(SELECT string_agg(ec.codigo_etapa_contenido::TEXT, ',') FROM core.etapa_contenido as ec
            	JOIN core.etapa_cont_event ON etapa_cont_event.codigo_etapa_contenido = ec.codigo_etapa_contenido
            	WHERE  etapa_cont_event.codigo_evento = evento.codigo_evento
            	AND etapa_cont_event.codigo_estado = ".Estado::getState('active') . ") as idetapas"),
      DB::raw("(SELECT string_agg(cc.nombre_categoria_contenido, ',') FROM core.categoria_contenido as cc
            	JOIN core.cat_conten_evento ON cat_conten_evento.codigo_categoria_contenido  = cc.codigo_categoria_contenido
            	WHERE  cat_conten_evento.codigo_evento = evento.codigo_evento) as categorias"),
      DB::raw("(SELECT string_agg(cc.codigo_categoria_contenido::TEXT, ',') FROM core.categoria_contenido as cc
            	JOIN core.cat_conten_evento ON cat_conten_evento.codigo_categoria_contenido = cc.codigo_categoria_contenido
            	WHERE  cat_conten_evento.codigo_evento = evento.codigo_evento) as idcategorias"),
      DB::raw("(SELECT string_agg(trim(t.texto_tag), ',') FROM core.tag_evento as te join core.tag as t ON te.codigo_tag = t.codigo_tag
                WHERE te.codigo_evento = evento.codigo_evento)  as tags")
    );
    $query->join('core.evento_institucion', 'evento_institucion.codigo_evento', '=', 'evento.codigo_evento');
    $query->join('core.tipo_evento_calendario', 'tipo_evento_calendario.codigo_tipo_evento_calendario' , '=','evento_institucion.codigo_tipo_evento_calendario' );
    $query->join('core.tipo_evento', 'tipo_evento.codigo_tipo_evento', '=', 'tipo_evento_calendario.codigo_tipo_evento');
    $query->leftJoin('core.ciudad', 'ciudad.codigo_ciudad', '=', 'evento.codigo_ciudad');
    $query->where('evento.codigo_privacidad_contenido', PrivacidadContenido::getPrivacy('public'));
    $query->where('evento.codigo_estado', Estado::getState('active'));
    $query->where('evento_institucion.codigo_estado', Estado::getState('active'));
    $query->where('tipo_evento_calendario.codigo_estado', Estado::getState('active'));
    $query->whereIn('evento.sync_algolia', [1,3]);
    $query->where('evento.codigo_evento', $id);
    $event = $query->first();

    if($event) {
      $this->info('Synchronizing index for event id '.$id);
      Log::info('Synchronizing index for event id '.$id);
      if($this->sycn('event', 'event.event', $event)){
        // Update state sync algolia
        $eventID = $event->id;
        Evento::where('codigo_evento', $eventID)->where('codigo_estado', Estado::getState('active'))->where('sync_algolia', 1)->update(['sync_algolia' => 0] );
        Evento::where('codigo_evento', $eventID)->where('codigo_estado', Estado::getState('active'))->where('sync_algolia', 3)->update(['sync_algolia' => 2]);
      }
      $this->info('Event index was successfully synchronized!');
      Log::info('Event index was successfully successfully synchronized!');
    }
  }
  public function indexMentor($id)
  {

    // Sync content mentor
    $query = Usuario::query();
    $query->select(DB::raw('DISTINCT ON(usuario.codigo_usuario) usuario.codigo_usuario as id'), 'usuario.sync_algolia',
    DB::raw("initcap( concat( trim(usuario.nombre_usuario), ' ', trim(persona.primer_apellido_persona) ) ) as title"),
    DB::raw('usuario.imagen_usuario as image'),
    DB::raw('especialidad.codigo_especialidad as especialidad'),
    DB::raw('initcap(especialidad.nombre_especialidad) as nombre_especialidad'),
    DB::raw('usuario.biografia_usuario as description'),
    DB::raw('usuario.nickname_usuario as url'),
    DB::raw("(SELECT string_agg(ep.nombre_etapa_proyecto::TEXT, ',') FROM core.etapa_proyecto as ep
        JOIN core.etapa_mentoria ON core.etapa_mentoria.codigo_etapa_proyecto = ep.codigo_etapa_proyecto 
        AND ep.codigo_estado = ".Estado::getState('active')."
        AND ep.codigo_institucion = usuario_institucion.codigo_institucion
        WHERE  etapa_mentoria.codigo_usuario = usuario.codigo_usuario
        AND etapa_mentoria.codigo_estado = ".Estado::getState('active') . ") as etapas"),
    DB::raw("(SELECT string_agg(ep.codigo_etapa_proyecto::TEXT, ',') FROM core.etapa_proyecto as ep
        JOIN core.etapa_mentoria ON core.etapa_mentoria.codigo_etapa_proyecto = ep.codigo_etapa_proyecto
        AND ep.codigo_estado = ".Estado::getState('active')." 
        AND ep.codigo_institucion = usuario_institucion.codigo_institucion
        WHERE  etapa_mentoria.codigo_usuario = usuario.codigo_usuario      
        AND etapa_mentoria.codigo_estado = ".Estado::getState('active'). ") as idetapas"),
    DB::raw('usuario_institucion.contador_like as likes'),
    DB::raw('usuario_institucion.contador_comentario as comments'),
    DB::raw("(SELECT string_agg(rui.codigo_institucion::text, ',') FROM seguridad.rol_usuario as rui 	
        JOIN core.usuario_institucion as usi ON usi.codigo_institucion = rui.codigo_institucion 
            AND usi.codigo_usuario = usuario.codigo_usuario
        JOIN seguridad.rol as roi ON roi.id =  rui.role_id
        JOIN seguridad.aceptacion_terminos as ati ON ati.codigo_usuario_institucion = usi.codigo_usuario_institucion
            AND ati.codigo_estado = ".Estado::getState('active')." AND ati.fecha_aceptacion_terminos IS NOT NULL
        JOIN param.version_terminos as vti ON vti.codigo_version_terminos = ati.codigo_version_terminos 
        JOIN param.terminos_condiciones as tci ON tci.codigo_terminos_condiciones = vti.codigo_terminos_condiciones 
            AND tci.tipo_terminos = ".TerminosCondiciones::getTypeTerms('mentoring')."
            AND tci.codigo_estado = ".Estado::getState('active')." 
        WHERE rui.user_id  = usuario.codigo_usuario
            AND usi.codigo_estado = ".Estado::getState('active')."
            AND rui.codigo_estado = ".Estado::getState('active')."
            AND roi.codigo_estado = ".Estado::getState('active')."
            AND rui.role_id = ".Rol::getRol('mentor')." ) as instituciones"),
    DB::raw("(SELECT string_agg(trim(t.texto_tag), ',') FROM core.tag_usuario as tu join core.tag as t ON tu.codigo_tag = t.codigo_tag
        WHERE tu.codigo_usuario = usuario.codigo_usuario) as tags")
    );
    $query->join('seguridad.rol_usuario', 'rol_usuario.user_id', '=', 'usuario.codigo_usuario');
    $query->join('seguridad.rol', 'rol.id', '=', 'rol_usuario.role_id');
    $query->join('core.persona', 'persona.codigo_usuario', '=', 'usuario.codigo_usuario');
    $query->join('core.usuario_institucion', function($join) {
    $join->on('usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario')
        ->on('usuario_institucion.codigo_institucion', '=', 'rol_usuario.codigo_institucion')
        ->where('usuario_institucion.codigo_estado', '=', Estado::getState('active') );
    });
    $query->join('seguridad.aceptacion_terminos', function($join) {
    $join->on('aceptacion_terminos.codigo_usuario_institucion', '=', 'usuario_institucion.codigo_usuario_institucion')
        ->where('aceptacion_terminos.codigo_estado', '=', Estado::getState('active') );
    });
    $query->join('param.version_terminos', 'version_terminos.codigo_version_terminos', '=', 'aceptacion_terminos.codigo_version_terminos');
    $query->join('param.terminos_condiciones', function($join) {
    $join->on('terminos_condiciones.codigo_terminos_condiciones', '=', 'version_terminos.codigo_terminos_condiciones')
        ->where('terminos_condiciones.codigo_estado', '=', Estado::getState('active') )
        ->where('terminos_condiciones.tipo_terminos', '=', TerminosCondiciones::getTypeTerms('mentoring'));
    });
    $query->leftJoin('core.especialidad', 'especialidad.codigo_especialidad', '=', 'persona.codigo_especialidad');
    $query->where('usuario.tipo_usuario', Usuario::typeUser('person'));
    $query->where('rol.id', Rol::getRol('mentor') );
    $query->whereRaw('aceptacion_terminos.fecha_aceptacion_terminos is not null');
    $query->where('usuario.codigo_estado', Estado::getState('active'));
    $query->where('rol_usuario.codigo_estado', Estado::getState('active'));
    $query->whereIn('usuario.sync_algolia', [1,3]);
    $query->where('usuario.codigo_usuario', $id);

    $mentor = $query->first();
    if($mentor) {
        $this->info('Synchronizing mentor index for mentor id '.$id);
        Log::info('Synchronizing mentor index for mentor id '.$id);
        if($this->sycn('mentor', 'mentoring.mentor', $mentor)){
        // Update state sync algolia
        $mentorID = $mentor->id;
        Usuario::where('codigo_usuario', $mentorID)->where('codigo_estado', Estado::getState('active'))->where('sync_algolia', 1)->update(['sync_algolia' => 0]);
        Usuario::where('codigo_usuario', $mentorID)->where('codigo_estado', Estado::getState('active'))->where('sync_algolia', 3)->update(['sync_algolia' => 2]);
        }
        $this->info('Mentor were successfully synchronized!');
        Log::info('Mentor were successfully synchronized!');
    }
  }
  public function indexProgram($id)
  {
    // Sync content program
    $query = Convocatoria::query();
    $query->select('convocatoria.sync_algolia',
      DB::raw('convocatoria.codigo_convocatoria as id'),
      DB::raw('convocatoria.nombre_convocatoria as title'),
      DB::raw('convocatoria.imagen_convocatoria as image'),
      DB::raw('convocatoria.resumen_convocatoria as description'),
      DB::raw('convocatoria.fecha_cierre_convocatoria as deadline'),
      DB::raw('convocatoria.url_convocatoria as url'),
      DB::raw('upper(trim(ue.nombre_usuario)) as entity_name'),
      DB::raw('ue.codigo_usuario as entity'),
      DB::raw('convocatoria.contador_inscritos as participants'),
      DB::raw("(SELECT string_agg(ep.nombre_etapa_proyecto, ',') FROM core.etapa_proyecto as ep
            	JOIN core.convocatoria_etapa ON convocatoria_etapa.codigo_etapa_proyecto = ep.codigo_etapa_proyecto 
            	WHERE  convocatoria_etapa.codigo_convocatoria = convocatoria.codigo_convocatoria
            	AND convocatoria_etapa.codigo_estado = ".Estado::getState('active') . ") as etapas"),
      DB::raw("(SELECT string_agg(ep.codigo_etapa_proyecto::TEXT, ',') FROM core.etapa_proyecto as ep
            	JOIN core.convocatoria_etapa ON convocatoria_etapa.codigo_etapa_proyecto = ep.codigo_etapa_proyecto 
            	WHERE  convocatoria_etapa.codigo_convocatoria = convocatoria.codigo_convocatoria
            	AND convocatoria_etapa.codigo_estado = ".Estado::getState('active') . ") as idetapas"),
      DB::raw("(SELECT string_agg(cat.nombre_categoria_proceso, ',') FROM core.categoria_proceso as cat
            	JOIN core.convocatoria_categoria ON  cat.codigo_categoria_proceso = convocatoria_categoria.codigo_categoria_proceso 
				WHERE  convocatoria_categoria.codigo_convocatoria = convocatoria.codigo_convocatoria
            	AND convocatoria_categoria.codigo_estado = ".Estado::getState('active') . ") as categorias"),
      DB::raw("(SELECT string_agg(cat.codigo_categoria_proceso::TEXT, ',') FROM core.categoria_proceso as cat
            	JOIN core.convocatoria_categoria ON  cat.codigo_categoria_proceso = convocatoria_categoria.codigo_categoria_proceso 
				WHERE  convocatoria_categoria.codigo_convocatoria = convocatoria.codigo_convocatoria
            	AND convocatoria_categoria.codigo_estado = ".Estado::getState('active') . ") as idcategorias"),
      DB::raw("(SELECT string_agg(coi.codigo_institucion::text, ',') FROM core.convocatoria_institucion as coi
            	WHERE coi.codigo_convocatoria = convocatoria.codigo_convocatoria AND coi.codigo_estado = ".Estado::getState('active') . ") as instituciones")

    );
    $query->join('core.convocatoria_institucion', 'convocatoria_institucion.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
    $query->leftjoin('seguridad.usuario as ue', 'ue.codigo_usuario', '=', 'convocatoria.seg_codigo_usuario');
    $query->leftjoin('core.entidad as en', 'en.codigo_usuario', '=', 'ue.codigo_usuario');
    $query->whereIn('convocatoria.codigo_privacidad_contenido', [PrivacidadContenido::getPrivacy('public')]);
    $query->where('convocatoria.codigo_estado', Estado::getState('active'));
    $query->where('convocatoria_institucion.codigo_estado', Estado::getState('active'));
    $query->whereIn('convocatoria.sync_algolia', [1,3]);
    $query->where('convocatoria.codigo_convocatoria', $id);
    $query->groupBy('convocatoria.codigo_convocatoria');
    $query->groupBy('convocatoria.sync_algolia');
    $query->groupBy('ue.nombre_usuario');
    $query->groupBy('ue.codigo_usuario');
    
    $program = $query->first();
    if($program) {

      $this->info('Synchronizing for program id '.$id);
      Log::info('Synchronizing for program id '.$id);
      if($this->sycn('program', 'program.program', $program)){
        // Update state sync algolia
        $programID = $program->id;
        Convocatoria::where('codigo_convocatoria', $programID)->where('codigo_estado', Estado::getState('active'))->where('sync_algolia', 1)->update(['sync_algolia' => 0] );
        Convocatoria::where('codigo_convocatoria', $programID)->where('codigo_estado', Estado::getState('active'))->where('sync_algolia', 3)->update(['sync_algolia' => 2]);
      }
      $this->info('Program index was successfully synchronized!');
      Log::info('Program index was successfully synchronized!');
    }
  }

  public function sycn($type, $lang, $item)
  {

      $objectID = sprintf('%s-%s', $type, $item->id);
     
      $object = [
        'objectID' => $objectID,
        'type' => $type,
        'title' => trim($item->title),
        'description' => trim($item->description),
        'image' => $item->image,
        'url' => trim($item->url),
        'url_category' => isset($item->url_category) ? trim($item->url_category)  : '',
        'likes' => $item->likes ?? 0,
        'comments' => $item->comments ?? 0,
        'participants' => $item->participants ?? 0,
        'stage' => $item->stage ?? '',
        'deadline' => $item->deadline ?? '',
        'location' => $item->location ?? '',
        'entity'=>$item->entity ?? '',
        'entity_name'=>$item->entity_name ?? '',
        'speciality'=>$item->especialidad ?? '',
        'speciality_name'=>$item->nombre_especialidad ?? '',
        'precio'=>$item->precio ?? '',
        'category_program' =>$item->category_program ?? '',
        'lang' => $lang,
        '_stages' => $item->etapas ?? '' ,
        '_stagesid' => ($item->idetapas ?  array_unique( array_map('intval', explode(',', $item->idetapas))) : []),
        '_institutions' => ($item->instituciones ?  array_unique( array_map('intval', explode(',', $item->instituciones))) : []),
        '_categories' => $item->categorias ? $item->categorias : '',
        '_categoriesid' => ($item->idcategorias ?  array_unique(array_map('intval', explode(',', $item->idcategorias))) : []),
        '_tags' => ($item->tags ?  array_unique(explode(',', $item->tags)) : [])
      ]; 

 
         
      if($this->option('dev') || $this->option('log')){           
        switch ($item->sync_algolia) {
          // Por Sincronizar
          case '1':
  					$res = $this->testSave($object, 'save index');
            break;


          // 3 - Eliminar indice
          case '3':
          $res = $this->testSave($object, 'delete index');
          break;
        }
       
      }elseif($this->remote_sync ||  $this->option('force')){           
        switch ($item->sync_algolia) {
          // Por Sincronizar
          case '1':
  					$res = $this->client->saveObject("content", $object);
            break;


          // 3 - Eliminar indice
          case '3':
            $this->client->deleteObject("content", $objectID);
            break;
        }
        
      }
 
    $this->info('');
    return ($this->remote_sync ||  $this->option('force') || $this->option('dev') );

  }

  private function testSave(array $data, $case = 'testsave')
  {
    Log::info('Algolia ' . $case . ' ' . $data['objectID']);
    Log::info(['data-'.$data['objectID']=>$data]);
  }

}
