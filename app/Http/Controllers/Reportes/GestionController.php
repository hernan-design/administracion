<?php

namespace App\Http\Controllers\Reportes;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Auth, AWS, Datatables, TuProyecto, Log;

class GestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $reports = [];
            $s3 = AWS::createClient('s3');

            $results = $s3->ListObjects([
                'Bucket' => env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com'),
                'Prefix' => 'reports/'.session('institution')->codigo_institucion.'/'.Auth::user()->codigo_usuario
            ]);

            if(!is_null($results['Contents'])){
                foreach ($results['Contents'] as $object) {
                    list($folder, $institution, $user, $name_report) = explode("/", $object['Key']);

                    $data['key'] = $object['Key'];
                    $data['name'] =  $name_report;
                    $data['date'] =  date('Y-m-d H:i:s', strtotime($object['LastModified']));
                    $data['size'] =  TuProyecto::formatBytes($object['Size']);
                    $data['download'] = config('tuproyecto.url_download_aws')."/{$object['Key']}";

                    $reports[] = $data;
                }
            }

            return Datatables::of($reports)->make(true);
        }

        return view('reports.management.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $deleteReport = Storage::delete('reports/'.session('institution')->codigo_institucion.'/'.Auth::user()->codigo_usuario.'/'.$id);
            if(!$deleteReport){
                throw new \Exception("Reporte no eliminado {$id}");
            }

            return response()->json(['success' => true, 'message' => 'Se ha eliminado el reporte correctamente.']);
        }catch(\Exception $e){
            Log::error(sprintf('%s:%s', 'GestionController:destroy', $e->getMessage()));
            return response()->json(['success' => false, 'errors' => trans('app.exception')]);
        }

    }
}
