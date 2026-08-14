<?php

namespace App\Models\Param;

use Illuminate\Database\Eloquent\Model;

class ItemMenu extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'param.item_menu';

	protected $primaryKey = 'codigo_item_menu';

	// public static function getClass($class) {
    // 	switch ($class) :
	// 	    case 'person': return 1;
	// 	    case 'entity': return 2;
	// 		default: return 'Not class identified';
	// 	endswitch;
    // }

	// public static function getTypeDocument($document) {
    // 	switch ($document) :
	// 	    case 'rut': return 1;
	// 	    case 'chamberCommerce': return 2;
	// 	    case 'Brochure': return 3;
	// 	    case 'other': return 4;
	// 	    case 'image': return 5;
	// 	    case 'document': return 6;
	// 	    case 'summary': return 6;
	// 	    case 'curriculum': return 10;
	// 		default: return 'Not document identified';
	// 	endswitch;
    // }
}
