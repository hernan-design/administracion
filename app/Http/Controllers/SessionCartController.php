<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SessionCartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        if(!Input::has('_key')) {
            return Response::json(array('success' => false, 'error' => 'Debe existir [_key] para agregar item.'));  
        }
        if(!Input::has('_template')) {
            return Response::json(array('success' => false, 'error' => 'Debe existir [_template] para agregar item.')); 
        }
        $list = SessionCart::addItem(Input::all());
        
        return Response::json(array('success' => true, 'list' => $list));
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
        if(!Input::has('_key')) {
            return Response::json(array('success' => false, 'error' => 'Debe existir [_key] para eliminar item.')); 
        }
        if(!Input::has('_template')) {
            return Response::json(array('success' => false, 'error' => 'Debe existir [_template] para eliminar item.'));    
        }
        $list = SessionCart::delItem($id, Input::get('_key'), Input::get('_template'));
        return Response::json(array('success' => true, 'list' => $list));
    }
}
