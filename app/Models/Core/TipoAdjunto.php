<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class TipoAdjunto extends Model
{
    protected $table = 'core.tipo_adjunto';

    protected $primaryKey = 'codigo_tipo_adjunto';

    public static function getTypeAttached($attached) {
    	switch ($attached) :
		    case 'document': return 1;
		    case 'youtube': return 2;
		    case 'flash': return 3;
		    case 'vimeo': return 4;
		    case 'html': return 5;
		    case 'image': return 6;
		    case 'gallery': return 7;
			case 'link': return 8;
			case 'survey': return 9;
			case 'excel': return 10;
			default: return 'Not attached identified';
		endswitch;
    }

	public static function getByThemesType()
	{
		$query = TipoAdjunto::query();
		$query->whereIn('codigo_tipo_adjunto', [TipoAdjunto::getTypeAttached('document'),
			TipoAdjunto::getTypeAttached('youtube'), TipoAdjunto::getTypeAttached('vimeo'),
			TipoAdjunto::getTypeAttached('html'), TipoAdjunto::getTypeAttached('link'),  TipoAdjunto::getTypeAttached('survey')] );
		return $query->get();
	}

	public static function getAttachmentTypeList()
	{
		$query = TipoAdjunto::query();
		$query->where('codigo_estado',8);
		return $query->get();
	}

}
