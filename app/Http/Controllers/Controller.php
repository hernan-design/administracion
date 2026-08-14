<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Instantiate a new Controller instance.
     */
    public function __construct()
    {
        $this->middleware('ability:admin|super-admin|administrativo,view');
        $this->middleware('ability:admin|super-admin|administrativo,create', ['only' => ['create', 'store']]);
        $this->middleware('ability:admin|super-admin|administrativo,edit', ['only' => ['edit', 'update']]);
        $this->middleware('ability:admin|super-admin|administrativo,remove', ['only' => ['destroy']]);
        $this->middleware('ability:admin|super-admin|administrativo,export', ['only' => ['exportar']]);
    }
}
