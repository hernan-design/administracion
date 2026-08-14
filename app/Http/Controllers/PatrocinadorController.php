<?php

namespace App\Http\Controllers;

use App\Models\Aprende\PatrocinadorCurso;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Log, Storage;
use Datatables;

use App\Models\Aprende\Patrocinador;

class PatrocinadorController extends Controller
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
			switch ($request->data) {
				case 'list':
					return response()->json(Patrocinador::getOrganizersList());
					break;
				default:
                    //dd('ajax datos ',$request);
					//return response()->json(Patrocinador::getOrganizers());
                    return Datatables::of( Patrocinador::getOrganizers() )->make(true);
			}
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
       // Obtén el archivo de la solicitud
      $imagen = $request->file('imagen_patrocinador');
      $fileUrl = Storage::disk('s3')->put('imagenes/cursos/'.$request->codigo_curso, $imagen, 'public');

        if($request->ajax()){
			DB::beginTransaction();
			try {
				$organizer = new Patrocinador;
				$organizer->nombre_patrocinador = $request->nombre_patrocinador;
        $organizer->descripcion_patrocinador = $request->descripcion_patrocinador;
				$organizer->imagen_patrocinador = Storage::disk('s3')->url($fileUrl);
				$organizer->url_patrocinador = slugtp($request->nombre_patrocinador, $organizer->getTable(), 'url_patrocinador');
				$organizer->save();

				// Commit Transaction
				DB::commit();

				return response()->json(['success' => true, 'message' => 'El organizador ha sido creado correctamente!!']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'PatrocinadorController:store', $e->getMessage()));
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
    public function destroy($id)
    {

			DB::beginTransaction();
			try {
                $organizer = Patrocinador::find($id)->delete();
                //dd($organizer);
				// Commit Transaction
				DB::commit();;
				return response()->json(['success' => true, 'message' => 'El organizador ha sido borrado!!']);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'PatrocinadorController:store', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}


    }
}
