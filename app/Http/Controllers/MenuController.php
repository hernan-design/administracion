<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Lang, App, Log;
use App\Models\Param\Menu, App\Models\Param\MenuInstitucion, App\Models\Param\SubMenuInstitucion, App\Models\Core\Estado;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            if($request->has('type')) {
                return response()->json(Menu::getDataMenu($request->type));
            }
        }
        return view('menu.main');
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
    public function destroy(Request $request,$id)
    {
        if ($request->ajax()) {
            $institutionMenu = MenuInstitucion::find($id);
            if(!$institutionMenu instanceof MenuInstitucion) {
                abort(404);
            }

            DB::beginTransaction();
            try {
                // Valid Submenu items
                $validSubMenu = SubMenuInstitucion::where('par_codigo_menu_institucion',$institutionMenu->codigo_menu_institucion)->get();
                if(count($validSubMenu) > 0){
                    return response()->json(['success' => false, 'errors' => 'error elimina primero los item menus hijo']);
                }
                // Set removed menu institution
                $institutionMenu->codigo_estado = Estado::getState('inactive');
                $institutionMenu->save();

                DB::commit();
                return response()->json(['success' => true]);
            }catch(\Exception $e){
                DB::rollback();
                Log::error($e->getMessage());
                return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
            }
        }
        abort(404);
    }
}
