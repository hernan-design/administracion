<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Datatables, Log, DB, Auth;
use App\Models\Core\Canal, App\Models\Core\Estado;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CanalController extends Controller
{
	/**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view,comunidad/canal');
        $this->middleware('ability:admin|super-admin|administrativo,create,comunidad/canal', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit,comunidad/canal', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove,comunidad/canal', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request->ajax()){
			return Datatables::of( Canal::getChannels( $request->all() ) )->make(true);
		}

		$states = Estado::getStates([], [Estado::getState('active'), Estado::getState('inactive')], $filter = null);
        return view('comunity.channel.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$channelType = Canal::$type;
        return view('comunity.channel.create-edit', compact('channelType'));
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
			$data = $request->all();

			$channel = new Canal;
			if ($channel->isValid($data)) {
				DB::beginTransaction();
	            try {
					$channel->fill($data);
					$channel->codigo_estado = Estado::getState('active');
					$channel->codigo_institucion = session('institution')->codigo_institucion;
					$channel->url_canal = slugtp($request->nombre_canal, $channel->getTable(), 'url_canal', true);
					$channel->save();

                    // Commit Transaction
                    DB::commit();
                    return response()->json(['success' => true, 'message' => 'El canal ha sido creado correctamente!!', 'codigo_canal' => $channel->codigo_canal, 'redirect' => true]);

				}catch(\Exception $e){
	                DB::rollback();
	                Log::error(sprintf('%s:%s', 'CanalController:store', $e->getMessage()));
	                return response()->json(['success' => false, 'errors' => trans('app.exception')]);
	            }
			}
			return response()->json(['success' => false, 'errors' => $channel->errors]);
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
		if(!is_numeric($id)) {
            abort(404);
        }

		$channel = Canal::getChannelById($id);
		if(!$channel instanceof Canal){
			abort(404);
		}
		$channelType = Canal::$type;
		return view('comunity.channel.create-edit', compact('channel', 'channelType'));
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
		if($request->ajax()){
			$data = $request->all();

			$channel = Canal::getChannelById($id);
			if(!$channel instanceof Canal){
				return response()->json(['success' => false, 'errors' => 'El código del canal no se encuentra en el sistema ']);
			}

			DB::beginTransaction();
			try {
				if($request->has('change_state') && !empty($request->change_state)){
					$channel->codigo_estado = $channel->codigo_estado == Estado::getState('active') ? Estado::getState('inactive') : Estado::getState('active');
				}else {
					$channel->fill($data);
				}

				$channel->save();

				// Commit Transaction
				DB::commit();
				return response()->json(['success' => true, 'message' => 'El canal ha sido editado correctamente!!', 'codigo_canal' => $channel->codigo_canal, 'codigo_estado' => $channel->codigo_estado]);

			}catch(\Exception $e){
				DB::rollback();
				Log::error(sprintf('%s:%s', 'CanalController:update', $e->getMessage()));
				return response()->json(['success' => false, 'errors' => trans('app.exception')]);
			}
		}
		abort(404);
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
