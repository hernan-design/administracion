<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class SectorInteres extends Model
{
	protected $table = 'core.sector_interes';

   	protected $primaryKey = 'codigo_sector_interes';

   	public static function getSectorByActivity($activity_code) {
        return SectorInteres::select(
                'sector_economico.nombre_sector_economico'
            )
            ->join('core.sector_economico','sector_interes.codigo_sector_economico','=','sector_economico.codigo_sector_economico')
            ->where('sector_interes.codigo_sector_interes', '=', $activity_code)
            ->first();
    }
}
