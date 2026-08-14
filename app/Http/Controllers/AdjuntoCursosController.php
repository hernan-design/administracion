<?php

namespace App\Http\Controllers;

use App\Models\Aprende\Curso, App\Models\Aprende\AdjuntoCurso, App\Models\Core\Estado, App\Models\Core\Adjunto;

use Illuminate\Http\Request;
use Input, Log, Lang, DB;

class AdjuntoCursosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $course = Curso::getAttachedCourse(Input::get('codigo_curso'));
        return response()->json($course);
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
            try {
                if ($request->hasFile('file')) {
                    if ($request->has('codigo_curso')) {
                        $code_course = $request->codigo_curso;
                        $file = $request->file('file');

                        if (!$code_course && !is_numeric($code_course)) {
                            abort(404);
                        }

                        $course = Curso::find($code_course);
                        if (!$course instanceof Curso) {
                            abort(404);
                        }

                        $result = Curso::uploadAttachCourse($file->getRealPath(), $file->getClientOriginalName(), $code_course);

                        return response()->json(['success' => true, 'file_path' => $result->file_path]);
                    }
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
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
            if ($request->has('codigo_curso')) {
                DB::beginTransaction();
                try {
                    $code_course = $request->codigo_curso;
                    $code_attach = $request->codigo_adjunto;

                    if (!$code_course && !is_numeric($code_course)) {
                        abort(404);
                    }

                    $course = Curso::find($code_course);
                    if (!$course instanceof Curso) {
                        abort(404);
                    }

                    AdjuntoCurso::where('codigo_adjunto', '=', $code_attach)->where('codigo_curso', '=', $code_course)->update(['codigo_estado' => Estado::getState('removed')]);

                    DB::commit();
				    return response()->json(['success' => true]);
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'Description' => 'Error inesperado' ]);
                }

            }
        }
    }
}
