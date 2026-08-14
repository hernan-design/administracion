<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

use DB, Input, Auth, Validator, Lang, Log;

class ForoInstitucion extends Model {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'core.foro_institucion';

  public $timestamps = false;

  public $incrementing = false;
}

