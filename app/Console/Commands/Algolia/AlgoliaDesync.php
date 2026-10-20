<?php
namespace App\Console\Commands\Algolia;
use Illuminate\Console\Command;
use URL, DB, Log;
use App\Models\Core\Contenido,App\Models\Core\Convocatoria, App\Models\Core\Estado, App\Models\Core\Foro,
  App\Models\Aprende\Curso, App\Models\Core\Producto, App\Models\Core\Evento, App\Models\Core\PrivacidadContenido, 
  App\Models\Seguridad\Usuario, App\Models\Seguridad\Rol, App\Models\Param\TerminosCondiciones;

  class AlgoliaDesync extends Command
  {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'algolia:desync 
                            {--F|force : Force desync to algolia ignoring config algolia_sync parameter value}
                            {--D|dev : Print to LOG and desync objects}
                            {--L|log : Print to LOG  but don\'t desync objects}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desync content index Algolia';
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
  
      if($this->option('dev')){ 
        $this->info('Algolia Desync is on dev mode!');
        Log::warning('Algolia Desync is on dev mode!');
      }elseif($this->option('log')) {
        $this->info('Algolia Desync is on log mode!');
        Log::warning('Algolia Desync is on log mode!');
      }
      elseif(!$this->remote_sync &&  !$this->option('force')) {
        $this->info('Algolia Desync is off!');   
        Log::warning('Algolia Desync is off!');
      }
  
      // $this->indexBlog();
      // $this->indexForum();
      // $this->indexCourse();
      // $this->indexProduct();
      // $this->indexEvent();
      // $this->indexMentor();
      // $this->indexProgram();
  
      $this->info('Algolia Desync completed!');
  
    }

    public function indexBlog()
    {
      // Desync content blog
      $query = Contenido::query();
      $query->select(DB::raw('contenido.codigo_contenido as id'), 'contenido.*');
      $query->where(function ($query) {
        $query->whereIn('contenido.codigo_estado', [ Estado::getState('inactive'), Estado::getState('removed') ]);
        $query->whereNotIn('contenido.sync_algolia',[2]);
      });
      $query->orWhere(function ($query) {
          $query->whereIn('contenido.imagen_contenido', [
              'https://lh6.googleusercontent.com/-NtJLiTZeioY/Uk8mE2BraXI/AAAAAAAABlc/0J7qX7azPUo/w370-h206-c/sinimagenproducto.png'
            ])->whereNotIn('contenido.sync_algolia',[2]);
      });
      $query->orWhere(function ($query) {
          $query->whereIn('contenido.sync_algolia', [3]);
      });
  
      $articles = $query->get();
      if($articles->count()) {
        $this->info('Desynchronizing articles index...');
        Log::info('Desynchronizing articles index...');
        if($this->desycn('article', 'blog.article', $articles)){
              $articlesID = $articles->pluck('id')->all();
              // Update state desync algolia    
              Contenido::whereIn('codigo_contenido', $articlesID)->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]);
        }
        $this->info('Articles were successfully desynchronized!');
        Log::info('Articles were successfully desynchronized!');
      }
    }
  
    public function indexForum()
    {
      // Desync content forum
      $query = Foro::query();
      $query->select( DB::raw('foro.codigo_foro as id'), 'foro.*');
      $query->where(function ($query) {
        $query->whereIn('foro.codigo_estado', [ Estado::getState('inactive'), Estado::getState('removed') ]);
        $query->whereNotIn('foro.sync_algolia', [2]);
      });
      $query->orWhere(function ($query) {
          $query->where('foro.foro_grupo', true)
          ->whereNotIn('foro.sync_algolia',[2]);
      });
      $query->orWhere(function ($query) {
          $query->whereIn('foro.sync_algolia', [3]);
      });

      $forums = $query->get();
      if($forums->count()) {
        $this->info('Desynchronizing forums index...');
        Log::info('Desynchronizing forums index...');
        if($this->desycn('forum', 'forum.forum', $forums)){
          // Update state desync algolia
          $forumsID = $forums->pluck('id')->all();   
          Foro::whereIn('codigo_foro', $forumsID)
          ->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]);   
        }      
        $this->info('Forums were successfully desynchronized!');
        Log::info('Forums were successfully desynchronized!');
      }
    }

    public function indexCourse()
    {
      // Desync content course
      $query = Curso::query();
      $query->select(DB::raw('curso.codigo_curso as id'), 'curso.*');
      $query->where(function ($query) {
        $query->whereIn('curso.codigo_estado', [ Estado::getState('inactive'), Estado::getState('removed') ]);
        $query->whereNotIn('curso.sync_algolia', [2]);
      });
      $query->orWhere(function ($query) {
        $query->whereNotIn('curso.codigo_privacidad_contenido', [PrivacidadContenido::getPrivacy('public')]);
        $query->whereNotIn('curso.sync_algolia', [2]);
      });
      $query->orWhere(function ($query) {
          $query->whereIn('curso.sync_algolia', [3]);
      });

      $courses = $query->get();
      if($courses->count()) {
          $this->info('Desynchronizing courses index...');
          Log::info('Desynchronizing courses index...'); 
          if($this->desycn('course', 'learn.course', $courses)){
              // Update state desync algolia
              $coursesID = $courses->pluck('id')->all();   
              Curso::whereIn('codigo_curso', $coursesID)
              ->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]);
          }
          $this->info('Courses were successfully desynchronized!');
          Log::info('Courses were successfully desynchronized!'); 
      }
    }

    public function indexProduct()
    {
      // Desync content product
      $query = Producto::query();
      $query->select(DB::raw('producto.codigo_producto as id'), 'producto.*');
      $query->where(function ($query) {
        $query->whereIn('producto.codigo_estado', [ Estado::getState('inactive'), Estado::getState('removed') ]);
        $query->whereNotIn('producto.sync_algolia', [2]);
      });
      $query->orWhere(function ($query) {
          $query->whereIn('producto.sync_algolia', [3]);
      });
      
      $products = $query->get();
      if($products->count()) {
        $this->info('Desynchronizing products index...');
        Log::info('Desynchronizing products index...'); 
        if($this->desycn('product', 'product.product', $products)){
          // Update state desync algolia
          $productsID = $products->pluck('id')->all();   
          Producto::whereIn('codigo_producto', $productsID)
              ->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]); 
        }
        $this->info('Products were successfully desynchronized!');
        Log::info('Products were successfully desynchronized!');
      }
    }

    public function indexEvent()
    {
      // Desync content event
      $query = Evento::query();
      $query->select(DB::raw('evento.codigo_evento as id'),'evento.*');
      $query->leftJoin('core.evento_institucion', 'evento_institucion.codigo_evento', '=', 'evento.codigo_evento');
      $query->leftJoin('core.tipo_evento_calendario', 'tipo_evento_calendario.codigo_tipo_evento_calendario' , '=','evento_institucion.codigo_tipo_evento_calendario' );
      $query->where(function ($query) {
        $query->whereIn('evento.codigo_estado', [ Estado::getState('inactive'), Estado::getState('removed') ]);
        $query->whereNotIn('evento.sync_algolia', [2]);
      });
      $query->orWhere(function ($query) {
          $query->whereIn('evento_institucion.codigo_estado', [ Estado::getState('inactive'), Estado::getState('removed') ]);
          $query->whereNotIn('evento.sync_algolia', [2]);
      });
      $query->orWhere(function ($query) {
        $query->whereNotIn('evento.codigo_privacidad_contenido', [PrivacidadContenido::getPrivacy('public')]);
        $query->whereNotIn('evento.sync_algolia', [2]);
      });
      $query->orWhere(function ($query) {
          $query->whereIn('tipo_evento_calendario.codigo_estado', [ Estado::getState('inactive'), Estado::getState('removed') ]);
          $query->whereNotIn('evento.sync_algolia', [2]);
      });
      $query->orWhere(function ($query) {
          $query->whereIn('evento.sync_algolia', [3]);
      });

      $events = $query->get();
      if($events->count()) {
        $this->info('Desynchronizing events index...');
        Log::info('Desynchronizing events index...');
        if($this->desycn('event', 'event.event', $events)){
          // Update state desync algolia
          $eventsID = $events->pluck('id')->all();   
          Evento::whereIn('codigo_evento', $eventsID)
              ->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]); 
        }
        
        $this->info('Events were successfully desynchronized!');
        Log::info('Events were successfully desynchronized!');
      }
    }

    public function indexMentor()
    {
      // Desync content mentor
      $query = Usuario::query();
      $query->select(DB::raw('usuario.codigo_usuario as id'),'usuario.*');
      $query->leftJoin('seguridad.rol_usuario', 'rol_usuario.user_id', '=', 'usuario.codigo_usuario');
      $query->leftJoin('seguridad.rol', 'rol.id', '=', 'rol_usuario.role_id');
      $query->leftJoin('core.usuario_institucion', 'usuario_institucion.codigo_usuario', '=', 'usuario.codigo_usuario');
      $query->where(function ($query) {
        $query->whereIn('usuario.codigo_estado', [ Estado::getState('inactive'), Estado::getState('removed') ]);
        $query->whereNotIn('usuario.sync_algolia', [2]);
    });   
      $query->orWhere(function ($query) {
          $query->whereNotIn('usuario.tipo_usuario', [ Usuario::typeUser('person') ]);
          $query->whereNotIn('usuario.sync_algolia', [2]);
      });    
      $query->orWhere(function ($query) {
          $query->whereNotIn('rol.id', [ Rol::getRol('mentor') ]);
          $query->whereNotIn('usuario.sync_algolia', [2]);
      });
      $query->orWhere(function ($query) {
          $query->whereIn('usuario_institucion.codigo_estado', [ Estado::getState('inactive'), Estado::getState('removed') ]);
          $query->whereNotIn('usuario.sync_algolia', [2]);
      });
      $query->orWhere(function ($query) {
          $query->whereIn('rol_usuario.codigo_estado', [ Estado::getState('inactive'), Estado::getState('removed') ]);
          $query->whereNotIn('usuario.sync_algolia', [2]);
      });
      $query->orWhere(function ($query) {
          $query->whereIn('usuario.sync_algolia', [3]);
      });
  
      $mentors = $query->get();
      if($mentors->count()) {
        $this->info('Desynchronizing mentors index...');
        Log::info('Desynchronizing mentors index...');
        if($this->desycn('mentor', 'mentoring.mentor', $mentors)){
          // Update state desync algolia
          $mentorsID = $mentors->pluck('id')->all();   
          Usuario::whereIn('codigo_usuario', $mentorsID)
              ->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]); 
        }
        $this->info('Mentors were successfully desynchronized!');
        Log::info('Mentors were successfully desynchronized!');
      }
    }

    public function indexProgram()
    {
      // Desync content program
      $query = Convocatoria::query();
      $query->select(DB::raw('convocatoria.codigo_convocatoria as id'),'convocatoria.*');
      $query->leftJoin('core.convocatoria_institucion', 'convocatoria_institucion.codigo_convocatoria', '=', 'convocatoria.codigo_convocatoria');
      $query->where(function ($query) {
        $query->whereIn('convocatoria.codigo_estado', [ Estado::getState('inactive'), Estado::getState('removed') ]);
        $query->whereNotIn('convocatoria.sync_algolia', [2]); 
      });   
      $query->orWhere(function ($query) {
          $query->whereIn('convocatoria_institucion.codigo_estado', [ Estado::getState('inactive'), Estado::getState('removed') ]);
          $query->whereNotIn('convocatoria.sync_algolia', [2]);
      });
      $query->orWhere(function ($query) {
        $query->wherenotIn('convocatoria.codigo_privacidad_contenido', [PrivacidadContenido::getPrivacy('public')]);
        $query->whereNotIn('convocatoria.sync_algolia', [2]);
    });
      
      $programs = $query->get();
      if($programs->count()) {       
        $this->info('Desynchronizing programs index...');
        Log::info('Desynchronizing programs index...');
        if($this->desycn('program', 'program.program', $programs)){
          // Update state desync algolia
          $programsID = $programs->pluck('id')->all();   
          Convocatoria::whereIn('codigo_convocatoria', $programsID)
              ->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]); 
        }   
        $this->info('Programs were successfully desynchronized!');
        Log::info('Programs were successfully desynchronized!');
      }
    }
  
    public function desycn($type, $lang, $data)
    {
      $progressBar = $this->output->createProgressBar($data->count());
      $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %memory:6s%');
      foreach ($data as $post)
      {
        $objectID = sprintf('%s-%s', $type, $post->id);
        $object =  $post->toArray();  
        $object['objectID'] =  $objectID; 
  
           
        if($this->option('dev') || $this->option('log')){           
          switch ($post->sync_algolia) {
              // No Sincronizar
              case '2':  			
                break;
    
              // Eliminar indice
              default:
              $res = $this->testSave($object, 'delete index');
                break;
            }
        }elseif($this->remote_sync ||  $this->option('force')){           
          switch ($post->sync_algolia) {
            // No Sincronizar
            case '2':  			
              break;
  
            // Eliminar indice
            default:
              $this->client->deleteObject("content", $objectID);
              break;
          }
          
        }
  
        $progressBar->advance();
      }
  
      $progressBar->finish();  
      $this->info('');    
      return ($this->remote_sync ||  $this->option('force') || $this->option('dev') );
  
    }
  
    private function testSave(array $data, $case = 'testsave')
    {
      Log::info('Algolia ' . $case . ' ' . $data['objectID']);
      Log::info(['data-'.$data['objectID']=>$data]);
    }
  
  }