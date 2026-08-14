<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use App\Models\Param\Institucion;

class SendEmail {

    /**
	 * Prepare and send email using SQS.
	 *
	 * @param data $array
	 */
    public static function notification($data)
    {

		// Validate params mail
		if(!isset($data['_template']) || trim($data['_template']) == '') {
			throw new \Exception('SendEmail Can not be empty _template');
		}

		if(!isset($data['_subject']) || trim($data['_subject']) == '') {
			throw new \Exception('SendEmail Can not be empty _subject');
		}

		if(!isset($data['_to']) || trim($data['_to']) == '') {
			throw new \Exception('SendEmail Can not be empty _to');
		}

		if(!isset($data['_inst'])) {
			throw new \Exception('SendEmail Can not be empty _inst');
		}

		$institution = Institucion::find($data['_inst']);
		if(!$institution instanceof Institucion) {
			throw new \Exception('SendEmail Can not be defined institution');
		}

		$data['_inst_color'] = $institution->color();
		$data['_inst_name'] = $institution->nombre_institucion;
		$data['_inst_slug'] = $institution->slug_institucion;
		$data['_inst_img'] =  $institution->imagen_notificacion_mail ?: $institution->logo_institucion;
		
		// Meta tags
		$meta_tags = $institution->metatags();
		$data['_inst_url'] = $institution->url_institucion ? $institution->url_institucion : config('tuproyecto.url');
		Mail::send($data['_template'], $data, function($message) use($data, $institution) {
 			$message->from(($institution->correo_envio_notificaciones ? $institution->correo_envio_notificaciones : config('tuproyecto.noreply')), $data['_inst_name']);
 			$message->subject($data['_subject']);
        	$message->to($data['_to'], isset($data['_to_name']) ? $data['_to_name'] : null);

            if(isset($data['to_copy'])) {
                $message->bcc($data['to_copy'], isset($data['to_copy_name']) ? $data['to_copy_name'] : null );
            }

        	if(isset($data['_attach']) && is_array($data['_attach'])) {
        		foreach ($data['_attach'] as $url => $name) {
        			$key = after(config('filesystems.disks.s3.bucket'), urldecode($url));
        			if(!Storage::has($key)) {
        				throw new \Exception("SendEmail Can not attach file $name: $url");
        			}
					$content = Storage::get($key);
        			$message->attachData($content, $name);
        		}
        	}
		});
	}

}
