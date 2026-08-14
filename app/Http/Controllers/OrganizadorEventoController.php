<?php

namespace App\Http\Controllers;

use App\Models\Core\Evento;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Log, Datatables;

use App\Models\Core\OrganizadorEvento, App\Models\Aprende\Patrocinador, App\Models\Core\Estado;

class OrganizadorEventoController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,evento');
        $this->middleware('ability:admin|super-admin|administrativo,create,evento', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,evento', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,evento', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
			return response()->json( OrganizadorEvento::getEventOrganizers( $request->all() ) );
		}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->ajax()){
			$organizer = Patrocinador::find($request->codigo_patrocinador);
			if(!$organizer instanceof Patrocinador){
				return response()->json(['success' => false, 'errors' => 'El organizador no existe, verifique.']);
			}

			$eventOrganizer = OrganizadorEvento::getEventOrganizer( $request->all() );
			if( !empty($eventOrganizer) ){
				return response()->json(['success' => false, 'errors' => 'El organizador ya está asignado, verifique.']);
			}

			DB::beginTransaction();
			try {
				$eventOrganizer = new OrganizadorEvento;
				$eventOrganizer->codigo_patrocinador = $request->codigo_patrocinador;
				$eventOrganizer->codigo_evento = $request->codigo_evento;
				$eventOrganizer->codigo_estado = Estado::getState('active');
				$eventOrganizer->save();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'El organizador ha sido asignado correctamente.', 'nombre_patrocinador' => $organizer->nombre_patrocinador, 'codigo_organizador_evento' => $eventOrganizer->codigo_organizador_evento]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'OrganizadorEventoController:store', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if($request->ajax()){
			if(!is_numeric($id)){
				return response()->json(['success' => false, 'errors' => 'El dato seleccionado no corresponde con la base de datos, verifique.']);
			}

			$eventOrganizer = OrganizadorEvento::getEventOrganizerById($id);
			if(!$eventOrganizer instanceof OrganizadorEvento){
				return response()->json(['success' => false, 'errors' => 'El patrocinador no se encuentra asignado, verifique.']);
			}

			DB::beginTransaction();
			try {
				$eventOrganizer->delete();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'El organizador ha sido eliminado correctamente.']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'EventoController:destroy', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
    }

    public function organizerEvent(Request $request) {
      if ($request->ajax()) {
        if ($request->has('codigo_evento') && $request->codigo_evento != "" && !$request->has('responsable')) {
          return Datatables::of(Evento::getOrganizerPrincipal($request->codigo_evento))->make(true);
        } else if ($request->has('tableModal') && !$request->has('responsable')) {
          return Datatables::of(Evento::getAllOrganizers($request->all()))->make(true);
        } else if ($request->has('codigo_evento') && $request->codigo_evento != "" && $request->has('responsable')) {
          return Datatables::of(Evento::getResponsableEvent($request->codigo_evento))->make(true);
        }
      }
    }
}
