<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'core.tag';

	protected $primaryKey = 'codigo_tag';

	public $timestamps = false;

	protected $fillable = ['texto_tag'];

	public static function tags(array $filters = [])
	{
		$query = Tag::query();
		$query->select('codigo_tag as id', 'texto_tag as value');
        if (isset($filters["term"]) && !empty($filters["term"])) {
            $query->where('texto_tag', 'ilike', '%'.$filters["term"].'%');
        }
        $query->orderBy('texto_tag','asc');
    	return $query->limit(30)->get();
	}
}
