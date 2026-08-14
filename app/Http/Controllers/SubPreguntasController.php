<?php

namespace App\Http\Controllers;

use App\Models\Core\SubpreguntaEncuesta;
use App\SubPreguntas;
use Illuminate\Http\Request;

class SubPreguntasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(SubpreguntaEncuesta::getAllSubquestions($request->all()));
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
     * @param  \App\SubPreguntas  $subPreguntas
     * @return \Illuminate\Http\Response
     */
    public function show(SubPreguntas $subPreguntas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SubPreguntas  $subPreguntas
     * @return \Illuminate\Http\Response
     */
    public function edit(SubPreguntas $subPreguntas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SubPreguntas  $subPreguntas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SubPreguntas $subPreguntas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SubPreguntas  $subPreguntas
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubPreguntas $subPreguntas)
    {
        //
    }
}
