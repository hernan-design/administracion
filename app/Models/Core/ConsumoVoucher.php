<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ConsumoVoucher extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.consumo_voucher';

   	protected $primaryKey = 'codigo_consumo_voucher';

   	public $timestamps = false;


   	public static function getConsumVoucher($purchase) {
      $query = ConsumoVoucher::query();
      $query->select('codigo_consumo_voucher', 'codigo_compra', 'codigo_detalle_voucher');
      $query->where('codigo_compra', '=', $purchase);
      $data = $query->first();

      return $data;
    }

}
