<?php

namespace App\Models\Core;

use App\Models\Seguridad\Usuario;
use DB, Auth, SendEmail;
use Datatables;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'core.voucher';

   	protected $primaryKey = 'codigo_voucher';

   	public $timestamps = false;

   	public static function getVoucher($voucher) {
    	switch ($voucher) :
        case 'innpulsa': return 'V20180213-1';
        case 'aldea': return 'V20200707-1';
        case 'rutan': return 'V20230330';
			default: return 'Not voucher identified';
		  endswitch;
    }

    public static function sendEmailCancelTransaction($product, $purchase, $seller, $value_purchase) {
   	    $person = Persona::where('codigo_usuario', Auth::user()->codigo_usuario)->first();
        $_product = Producto::find($product);
        $project = Voucher::getProjectOfPurchase($purchase);
        $user_seller = Voucher::getUserEntityBuyer($seller);

   	    if (!$person instanceof Persona) {
   	      abort(404);
          return response()->json(['success' => false, 'message' => "Persona no encontrada"]);
        }

   	    if (!$_product instanceof Producto) {
   	      abort(404);
          return response()->json(['success' => false, 'message' => "Producto no encontrado"]);
        }

        if (!isset($user_seller) && !empty($user_seller)) {
          abort(404);
          return response()->json(['success' => false, 'message' => "Usuario vendedor no encontrado"]);
        }

   	    if (!isset($project) && !empty($project)) {
          abort(404);
          return response()->json(['success' => false, 'message' => "Proyecto no encontrado"]);
        }

      // Send notification
      $data = [];
      $data['_inst'] = session('institution')->codigo_institucion;
      $data['_to'] = 'administracion@tuproyecto.com';
      $data['_to_name'] = ucwords(strtolower('Administrador'));
      $data['_name_person'] = ucwords(strtolower(Auth::user()->nombre_usuario.' '.$person->primer_apellido_persona));
      $data['_purchase'] = $purchase;
      $data['_name_product'] = $_product->nombre_producto;
      $data['_name_project'] = $project->nombre_usuario;
      $data['_name_seller'] =  ucwords(strtolower($user_seller->nombre_usuario));
      $data['_value_purchase'] = $value_purchase;
      $data['_subject'] = 'Cancelación de transacción';
      $data['_template'] = 'emails.voucher.cancel-transaction';
      SendEmail::notification($data);
    }

    public static function getProjectOfPurchase($purchase) {
        $query = Compra::query();
        $query->select('usuario.nombre_usuario');
        $query->join('core.consumo_voucher', 'consumo_voucher.codigo_compra', '=', 'compra.codigo_compra');
        $query->join('core.detalle_voucher', 'detalle_voucher.codigo_detalle_voucher', '=', 'consumo_voucher.codigo_detalle_voucher');
        $query->join('core.registro_fase_usuario', 'registro_fase_usuario.codigo_registro_fase_usuario', '=', 'detalle_voucher.codigo_registro_fase_usuario');
        $query->join('seguridad.usuario', 'usuario.codigo_usuario', '=', 'registro_fase_usuario.codigo_usuario');
        $query->where('consumo_voucher.codigo_compra', '=', $purchase);

        return $query->first();
    }

    public static function getUserEntityBuyer($seller) {
      $query = Entidad::query();
      $query->select('usuario.nombre_usuario');
      $query->join('seguridad.usuario', 'usuario.codigo_usuario', '=', 'entidad.codigo_usuario');
      $query->where('entidad.seg_codigo_usuario', '=', $seller);

      return $query->first();
    }
}
