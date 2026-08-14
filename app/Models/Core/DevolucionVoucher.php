<?php


namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class DevolucionVoucher extends Model
{
  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'core.devolucion_voucher';

  protected $primaryKey = 'codigo_devolucion_voucher';

  public $timestamps = false;

}
