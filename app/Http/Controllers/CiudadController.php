<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use TuProyecto;

use DB, Lang, App, Log, View, Auth, Input;

use App\Models\Core\Ciudad, App\Models\Core\Estado, App\Models\Seguridad\Usuario;

class CiudadController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request->ajax()){
			$data = [];
			$query = Ciudad::query();
            $query->select('codigo_ciudad as id',
				DB::raw("trim(initcap(nombre_ciudad) || ' - ' || COALESCE(initcap(distrito_ciudad), '') || ' - ' || COALESCE(pais.codigo_pais, '')) as text"));
            $query->join('core.pais', 'ciudad.codigo_pais', '=', 'pais.codigo_pais');
            $query->join('core.pais_institucion', function($join) {
                $join->on('pais.codigo_pais', '=', 'pais_institucion.codigo_pais')
                    ->where('pais_institucion.codigo_institucion', '=', session('institution')->codigo_institucion)
                    ->where('pais_institucion.codigo_estado', '=', Estado::getState('active'));
            });

			if($request->has('id')){
				$query->where("ciudad.codigo_ciudad", "=", $request->id);
			}

			if($request->has('q')) {
				$query->where( function($query) use($request) {
					$query->whereRaw("nombre_ciudad ilike '%".$request->q."%'");
					$query->orWhereRaw("distrito_ciudad ilike '%".$request->q."%'");
				});
			}

			if($request->has('data') && $request->data == 'user_city'){
				$query->whereIn('ciudad.codigo_ciudad', function($query)
				{
					$query->select(DB::raw(' distinct codigo_ciudad
					from "seguridad"."usuario"
					inner join "core"."usuario_institucion" on "usuario"."codigo_usuario" = "usuario_institucion"."codigo_usuario"
					and usuario_institucion.codigo_institucion = '.session('institution')->codigo_institucion.'
					where usuario.tipo_usuario = '.Usuario::typeUser('person').' '));
				});
			}

            $query->whereNotNull('distrito_ciudad');
            $query->whereRaw("distrito_ciudad != ''");
            $query->where('ciudad.codigo_estado', Estado::getState('active'));

            $query->orderby('distrito_ciudad','asc');
            $query->orderby('nombre_ciudad','asc');
			if(empty($request->q) && empty($request->id)) {
				$data = $query->limit( 50 )->get();
				return response()->json($data);
			}
            $data = $query->get();
            return response()->json($data);
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
        //
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
        //
    }
}
