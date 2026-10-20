<?php
namespace App\Console\Commands\Algolia;
use Illuminate\Console\Command;
use URL, DB, Log;
use App\Models\Core\Contenido,App\Models\Core\Convocatoria, App\Models\Core\Estado, App\Models\Core\Foro,
  App\Models\Aprende\Curso, App\Models\Core\Producto, App\Models\Core\Evento, App\Models\Core\PrivacidadContenido, 
  App\Models\Seguridad\Usuario, App\Models\Seguridad\Rol, App\Models\Param\TerminosCondiciones;

  class AlgoliaDesyncItem extends Command
  {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'algolia:desyncitem 
                            {type : Type of item} 
                            {id : Id of item}
                            {--F|force : Force desync to algolia ignoring config algolia_sync parameter value}
                            {--D|dev : Print to LOG and desync objects}
                            {--L|log : Print to LOG  but don\'t desync objects}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desync content index Algolia for Item';
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
  
    $type = $this->argument('type');
    $id = $this->argument('id');
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

            $this->info('Algolia Desync '.$type.'-id='.$id.' completed!');
        }else{
            $this->info('Algolia Desync was aborted because id='.$id.' is invalid for '.$type); 
            Log::warning('Algolia Desync was aborted because id='.$id.' is invalid for '.$type);
        } 
    }

    public function indexBlog($id)
    {
      // Desync content blog
      $query = Contenido::query();
      $query->select(DB::raw('contenido.codigo_contenido as id'), 'contenido.*');
      $query->where('codigo_contenido', $id);

      $article = $query->first();
      if($article) {
        $this->info('Desynchronizing index for article id '.$id);
        Log::info('Desynchronizing index for article id'.$id);
        if($this->desycn('article', 'blog.article', $article)){
              // Update state desync algolia    
              Contenido::where('codigo_contenido', $article->id)->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]);
        }
        $this->info('Article index was successfully desynchronized!');
        Log::info('Article index was successfully desynchronized!');
      }
    }
  
    public function indexForum($id)
    {
    
      // Desync content forum
      $query = Foro::query();
      $query->select( DB::raw('foro.codigo_foro as id'), 'foro.*');
      $query->where('codigo_foro', $id);

      $forum = $query->first();
      if($forum) {
        $this->info('Desynchronizing index for forum id '.$id);
        Log::info('Desynchronizing index for forum id '.$id);
        if($this->desycn('forum', 'forum.forum', $forum)){
          // Update state desync algolia
          Foro::where('codigo_foro', $forum->id)
          ->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]);   
        }      
        $this->info('Forum index was successfully desynchronized!');
        Log::info('Forum index was successfully desynchronized!');
      }
    }

    public function indexCourse($id)
    {
      // Desync content course
      $query = Curso::query();
      $query->select(DB::raw('curso.codigo_curso as id'), 'curso.*');
      $query->where('codigo_curso', $id);

      $course = $query->first();
      if($course) {
          $this->info('Desynchronizing index for course id '.$id);
          Log::info('Desynchronizing index for course id '.$id); 
          if($this->desycn('course', 'learn.course', $course)){
              // Update state desync algolia
              $courseID = $course->id;   
              Curso::where('codigo_curso', $courseID)
              ->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]);
          }
          $this->info('Course index was successfully desynchronized!');
          Log::info('Course index was successfully desynchronized!'); 
      }
    }

    public function indexProduct($id)
    {
      // Desync content product
      $query = Producto::query();
      $query->select(DB::raw('producto.codigo_producto as id'), 'producto.*');
      $query->where('codigo_producto', $id);
      
      $product = $query->first();
      if($product) {
        $this->info('Desynchronizing index for product id '.$id);
        Log::info('Desynchronizing index for product id '.$id); 
        if($this->desycn('product', 'product.product', $product)){
          // Update state desync algolia
          $productID = $product->id;   
          Producto::where('codigo_producto', $productID)
              ->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]); 
        }
        $this->info('Product index was successfully desynchronized!');
        Log::info('Product index was successfully desynchronized!');
      }
    }

    public function indexEvent($id)
    {
      // Desync content event
      $query = Evento::query();
      $query->select(DB::raw('evento.codigo_evento as id'),'evento.*');
      $query->where('evento.codigo_evento', $id);

      $event = $query->first();
      if($event) {
        $this->info('Desynchronizing index for event id '.$id);
        Log::info('Desynchronizing index for event id '.$id);
        if($this->desycn('event', 'event.event', $event)){
          // Update state desync algolia
          $eventID = $event->id;   
          Evento::where('codigo_evento', $eventID)
              ->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]); 
        }
        
        $this->info('Event index was successfully desynchronized!');
        Log::info('Event index was successfully desynchronized!');
      }
    }

    public function indexMentor($id)
    {
      // Desync content mentor
      $query = Usuario::query();
      $query->select(DB::raw('usuario.codigo_usuario as id'),'usuario.*');
      $query->where('usuario.codigo_usuario', $id);
  
      $mentor = $query->first();
      if($mentor) {
        $this->info('Desynchronizing index for mentor id '.$id);
        Log::info('Desynchronizing index for mentor id '.$id);
        if($this->desycn('mentor', 'mentoring.mentor', $mentor)){
          // Update state desync algolia
          $mentorID = $mentor->id;   
          Usuario::where('codigo_usuario', $mentorID)
              ->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]); 
        }
        $this->info('Mentor index was successfully desynchronized!');
        Log::info('Mentor index was successfully desynchronized!');
      }
    }

    public function indexProgram($id)
    {
      // Desync content program
      $query = Convocatoria::query();
      $query->select(DB::raw('convocatoria.codigo_convocatoria as id'),'convocatoria.*');
      $query->where('convocatoria.codigo_convocatoria', $id);
      
      $program = $query->first();
      if($program) {       
        $this->info('Desynchronizing for program id '.$id);
        Log::info('Desynchronizing for program id '.$id);
        if($this->desycn('program', 'program.program', $program)){
          // Update state desync algolia
          $programID = $program->id;   
          Convocatoria::where('codigo_convocatoria', $programID)
              ->whereNotIn('sync_algolia', [2])->update(['sync_algolia' => 2]); 
        }   
        $this->info('Program index was successfully desynchronized!');
        Log::info('Program index was successfully desynchronized!');
      }
    }
  
    public function desycn($type, $lang, $item)
    {
        $objectID = sprintf('%s-%s', $type, $item->id);
        $object =  $item->toArray();  
        $object['objectID'] =  $objectID; 
   
        if($this->option('dev') || $this->option('log')){           
            $res = $this->testSave($object, 'delete index');
        }elseif($this->remote_sync ||  $this->option('force')){           
           $this->client->deleteObject("content", $objectID);          
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