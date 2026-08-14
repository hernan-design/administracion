<?php

namespace App\Http\Controllers;

use App\Models\Core\Convocatoria;
use App\Models\Core\ConvocatoriaCategoria;
use App\Models\Core\Estado;
use Illuminate\Http\Request;
use DB;

class ConvocatoriaCategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if($request->ajax()){
        
      }
      abort(404);
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
        if ($request->ajax()) {
          DB::beginTransaction();
          try {
            $category = $request->data['option_id'];

            $program = Convocatoria::find($request->data['data_id']);
            if (!$program instanceof Convocatoria) {
              throw new \Exception("Convocatoria no encontrada {$request->data['data_id']}");
            }

            $programCategory = new ConvocatoriaCategoria();
            $programCategory->codigo_estado = Estado::getState('active');
            $programCategory->codigo_categoria_proceso = $category;
            $programCategory->codigo_convocatoria = $request->data['data_id'];
            $programCategory->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'La categoria ha sido añadida correctamente!!', 'codigo_convocatoria' => $request->data['data_id']]);
          } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'errors' => trans('app.exception')]);
          }

        }
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
       if ($request->ajax()) {
         DB::beginTransaction();
         try {
           $category = $request->categoria;

           $program = Convocatoria::find($request->data['data_id']);
           if (!$program instanceof Convocatoria) {
             throw new \Exception("Convocatoria no encontrada {$request->data['data_id']}");
           }

           ConvocatoriaCategoria::where('codigo_convocatoria', $request->data['data_id'])
             ->where('codigo_categoria_proceso', $category)
             ->delete();

           DB::commit();
           return response()->json(['success' => true, 'message' => 'La categoria ha sido desvinculada correctamente!!', 'codigo_convocatoria' => $request->data['data_id']]);
         } catch (\Exception $e) {
           DB::rollback();
           return response()->json(['success' => false, 'errors' => trans('app.exception')]);
         }
       }
    }
}
