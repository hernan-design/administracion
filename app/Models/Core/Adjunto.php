<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\TipoAdjunto, App\Models\Param\ParametroInstitucion, App\Models\Param\MetaInstitucion,  App\Models\Param\ParametroModulo;

use Config, App, AWS, Validator, Auth, TuProyecto;

class Adjunto extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.adjunto';

   	protected $primaryKey = 'codigo_adjunto';

   	public $timestamps = false;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['codigo_adjunto','codigo_tipo_adjunto', 'direccion_adjunto', 'nombre_adjunto'];

	/**
	* Valid create content.
	*/
	public function isValid($data)
	{
		$rules = [
			'codigo_tipo_adjunto'	=> 'required',
			'direccion_adjunto'		=> 'required',
			'nombre_adjunto'		=> 'required',
		];

		$validator = Validator::make($data, $rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->errors();
		return false;
	}

	/**
	* Upload resource to S3.
	*
	* @param  file  $file
	* @return Response
	*/
	public static function upload($file_path, $file_name, $type, $convocatoria = false, $compra = false)
	{
		if($file_path && $type) {
			try {
				$type = ($type == 'image') ? 'images' : 'documents';
				$s3 = AWS::createClient('s3');

				// Dynamic bucket
				$bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');
				if ($convocatoria && !$compra) {
          $key = sprintf('%s/%s/%d/%d/%s', $type, 'convocatorias', session('institution')->codigo_institucion, $convocatoria, $file_name);
        } else if (!$convocatoria && !$compra) {
          $key = sprintf('%s/%d/%d/%s', $type, date('Y'), date('m'), $file_name);
        } else if ($compra && !$convocatoria) {
          $key = sprintf('%s/%s/%s/%s-%s', $type, 'users', Auth::user()->codigo_usuario, date('YmdHis'), $file_name);
        }

				// Upload file to S3
				$result = $s3->putObject([
			        'Bucket' =>  $bucket,
			        'Key'    =>  $key,
			        'Body'   =>  fopen($file_path, 'rb'),
			        'ACL'    => 'public-read'
			    ]);

				$response = new \stdClass();
				$response->success = true;
				$response->file_path = $result['ObjectURL'];
				return $response;
			} catch (\Exception $e) {
				throw new \Exception($e->getMessage());
			}
		}
		throw new \Exception('file is undefined');
	}

	public static function uploadInstitutionAttachment($file_path, $file_name, $codigo_institucion, $nombre_institucion, $type) {
		if (isset($file_path)) {
			try {
				$s3 = AWS::createClient('s3');

				// Dynamic bucket
				$bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');

				$key = sprintf('%s/%s/%s/%s', $type, 'institutions', $nombre_institucion, $file_name);

				// Upload file to S3
				$result = $s3->putObject([
					'Bucket' =>  $bucket,
					'Key'    =>  $key,
					'Body'   =>  fopen($file_path, 'rb'),
					'ACL'    => 'public-read'
				]);

				$response = new \stdClass();
				$response->success = true;
				$response->file_path = $result['ObjectURL'];
				return $response;

			} catch (\Exception $e) {
				throw new \Exception($e->getMessage());
			}
		}
		throw new \Exception('file is undefined');
	}

  /**
   * Upload course attachment resource to S3.
   *
   * @param  file  $file
   * @return Response
   */
  public static function uploadCourseAttachment($file_path, $file_name, $codigo_curso, $codigo_tema)
  {
   	if($file_path) {
     	try {
       	$s3 = AWS::createClient('s3');

       	// Dynamic bucket
       	$bucket = env('AWS_BUCKET_NAME', 'static-dev.tuproyecto.com');
        $key = sprintf('%s/%d/%d/%d/%s', 'cursos', session('institution')->codigo_institucion, $codigo_curso, $codigo_tema ,$file_name);

        // Upload file to S3
        $result = $s3->putObject([
          'Bucket' =>  $bucket,
          'Key'    =>  $key,
          'Body'   =>  fopen($file_path, 'rb'),
          'ACL'    => 'public-read'
        ]);

        $response = new \stdClass();
        $response->success = true;
        $response->file_path = $result['ObjectURL'];
        return $response;
      } catch (\Exception $e) {
        throw new \Exception($e->getMessage());
      }
    }
    throw new \Exception('file is undefined');
  }

  public static function buildAttachmentInstitution($institution, $type, $parametro, $meta, $modulo, $code_parametro) {
		$adjunto = [];
		$object = new \stdClass();

		switch ($type) {
			case 'logo':
				if (isset($institution->logo_institucion)) {
					$object->uuid = TuProyecto::encryptSrcParams([$institution->logo_institucion]);
					$object->direccion_adjunto = $institution->logo_institucion;
					$object->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('image');
					$object->name = 'Logo Institucion';

					$adjunto[] = $object;

					return $adjunto;
				} else {
					return array();
				}
			break;
			case 'logoMail':
				if (isset($institution->imagen_notificacion_mail)) {
					$object->uuid = TuProyecto::encryptSrcParams([$institution->imagen_notificacion_mail]);
					$object->direccion_adjunto = $institution->imagen_notificacion_mail;
					$object->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('image');
					$object->name = 'Logo Mail Institucion';

					$adjunto[] = $object;

					return $adjunto;
				} else {
					return array();
				}
			break;
			case 'imageAdmin':
				if (isset($institution->logo_admin_institucion)) {
					$object->uuid = TuProyecto::encryptSrcParams([$institution->logo_admin_institucion]);
					$object->direccion_adjunto = $institution->logo_admin_institucion;
					$object->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('image');
					$object->name = 'Logo Administrador';

					$adjunto[] = $object;

					return $adjunto;
				} else {
					return array();
				}
			break;
			case 'imageReport':
				if (isset($institution->logo_reporte_institucion)) {
					$object->uuid = TuProyecto::encryptSrcParams([$institution->logo_reporte_institucion]);
					$object->direccion_adjunto = $institution->logo_reporte_institucion;
					$object->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('image');
					$object->name = 'Logo Reporte';

					$adjunto[] = $object;

					return $adjunto;
				} else {
					return array();
				}
			break;
			case 'imageLogoHeader':
				$parametrosInstitucion = ParametroInstitucion::where('codigo_institucion', '=', $institution->codigo_institucion)->where('codigo_parametro', '=', $parametro)->where('codigo_estado', '=', Estado::getState('active'))->first();

				if ($parametrosInstitucion instanceof ParametroInstitucion) {
					$object->uuid = TuProyecto::encryptSrcParams([$parametrosInstitucion->valor_parametro]);
					$object->direccion_adjunto = $parametrosInstitucion->valor_parametro;
					$object->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('image');
					$object->name = 'Logo Header';

					$adjunto[] = $object;

					return $adjunto;
				} else {
					return array();
				}

			break;
			case 'imageMeta':
				$metaInstitucion = MetaInstitucion::where('codigo_institucion', '=', $institution->codigo_institucion)->where('codigo_meta', '=', $meta)->where('codigo_estado', '=', Estado::getState('active'))->first();

				if ($metaInstitucion instanceof MetaInstitucion) {
					$object->uuid = TuProyecto::encryptSrcParams([$metaInstitucion->valor_meta_institucion]);
					$object->direccion_adjunto = $metaInstitucion->valor_meta_institucion;
					$object->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('image');
					if ($meta == 2) {
						$object->name = 'Logo Contenido Red Social';
					} else {
						$object->name = 'Logo Favicon';
					}

					$adjunto[] = $object;

					return $adjunto;
				} else {
					return array();
				}
			break;
			case 'imageModule':
				$parametroModuloInstitucion = ParametroModulo::where('codigo_institucion', '=', $institution->codigo_institucion)->where('codigo_modulo', '=', $modulo)->where('codigo_parametro', '=', $code_parametro)->where('codigo_estado', '=', Estado::getState('active'))->first();

				if ($parametroModuloInstitucion instanceof ParametroModulo) {
					$object->uuid = TuProyecto::encryptSrcParams([$parametroModuloInstitucion->valor_parametro_modulo]);
					$object->direccion_adjunto = $parametroModuloInstitucion->valor_parametro_modulo;
					$object->codigo_tipo_adjunto = TipoAdjunto::getTypeAttached('image');
					$object->codigo_modulo = $modulo;
					$object->name = 'Imagen Modulo';

					$adjunto[] = $object;

					return $adjunto;
				} else {
					return array();
				}

			break;
		}
  }
}
