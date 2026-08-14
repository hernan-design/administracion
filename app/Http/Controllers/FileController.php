<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use TuProyecto, Response, App, Lang, Log, Input, URL, File, Image, Storage;

use App\Models\Core\Adjunto;

class FileController extends Controller
{
	/**
     * Instantiate a new FileController instance.
     */
    public function __construct()
    {
       $this->middleware('auth');
    }

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

	/**
	 * Cropping resource.
	 *
	 * @return Response
	 */
	public function crop(Request $request)
	{
		if ($request->ajax()) {
			try {
				// Output file
				$output_file_path = sprintf('%s/temp/%s', public_path(), rand().'.jpg');
				$file_name = rand().'.jpg';

				if($request->has('file_name') && !empty($request->file_name))
				{
					switch ($request->crop_type) {
						case 'name':
							// Local file
							$input_file_path = sprintf('%s/temp/%s', public_path(), $request->file_name);
							if(!File::exists($input_file_path)) {
								throw new \Exception('file crop is undefined');
							}
							// open file a image resource
							$img = Image::make($input_file_path);
							break;

						case 'url':
							// Remote Input
							$input_file_path = sprintf('%s/temp/%s', public_path(), rand().'.'.$request->file_type);
							copy($request->file_name, $input_file_path);

							// open file a image resource
							$img = Image::make($input_file_path);
							break;

						default:
							throw new \Exception('crop type is undefined');
					}
				}else{
					return response()->json(['success' => false, 'errors' => 'Debe insertar una nueva imagen']);
					// throw new \Exception('file crop is undefined');
				}

				// crop image
				$img->crop( intval(Input::get('w')), intval(Input::get('h')), intval(Input::get('x')), intval(Input::get('y')) );
				if(Input::has('targ_w') && Input::has('targ_h')) {
					// now you are able to resize the instance
					$img->resize(Input::get('targ_w'), Input::get('targ_h'));
				}
				$img->save($output_file_path, 100);

				// Upload File
				$result = Adjunto::upload($output_file_path, $file_name, 'image', '', null);
				if(is_object($result) && $result->success === true) {
					return response()->json(['success' => true, 'message' => 'La imagen ha sido recortada correctamente!!', 'file_path' => $result->file_path]);
				}
			}catch (\Exception $e) {
				Log::error($e->getMessage());
				return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
			}
		}
    	App::abort(404);
	}


    /**
     * Temporary resource.
     *
     * @return Response
     */
    public function temp(Request $request)
    {
        if ($request->ajax()) {
            if ($request->hasFile('file')) {
                try {
                    $destinationPath = public_path().'/temp';

                    $file = $request->file('file');
                    $fileName = rand().'.'.$file->getClientOriginalExtension();
                    $mimeType = $file->getMimeType();

                    list($width, $height) = getimagesize($file->getRealPath());

                    $file->move($destinationPath, $fileName);
                    $file_path = sprintf('%s/temp/%s', URL::to('/'), $fileName);

					return response()->json(['success' => true, 'file_name' => $fileName, 'file_path' => $file_path, 'file_type' => $mimeType,
					    'file_width' => $width, 'file_height' => $height]);

                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                    return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
                }
            }
            return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
        }
        abort(404);
    }


	/**
	 * Temporary resource.
	 *
	 * @return Response
	 */
	public function upload(Request $request)
	{
		if ($request->ajax()) {
			if ($request->hasFile('file')) {
				try {
					$file = $request->file('file');
          $request->has('codigo_convocatoria') ? $convocatoria = $request->codigo_convocatoria : $convocatoria = null;
            if (!$convocatoria) {
              $result = Adjunto::upload($file->getRealPath(), $file->getClientOriginalName(), 'image', $convocatoria, null);
            } else {
              $result = Adjunto::upload($file->getRealPath(), $file->getClientOriginalName(), 'documents', $convocatoria, null);
            }
					return response()->json(['success' => true, 'file_path' => $result->file_path]);
				} catch (\Exception $e) {
					Log::error($e->getMessage());
          return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
				}
		    }
	    }
	}

  /**
   * upload course attachment file resource.
   *
   * @return Response
   */
  public function uploadCourseAttachment(Request $request)
  {

    if ($request->ajax()) {
    $data = $request->all();
    Log::debug(['data'=>$data]);
      if ($request->hasFile('file')) {


        try {
          $file = $request->file('file');

          $result = Adjunto::uploadCourseAttachment($file->getRealPath(), $request->qqfilename, $request->codigo_curso, $request->codigo_tema);

          return response()->json(['success' => true, 'file_path' => $result->file_path]);
        } catch (\Exception $e) {
          Log::error($e->getMessage());
          return response()->json(['success' => false, 'errors' => Lang::get('app.exception')]);
        }
      }
    }
  }
}
