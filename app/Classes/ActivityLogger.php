<?php

namespace App\Classes;

use Spatie\Activitylog\Support\ActivityLogger as SpatieActivityLogger;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;

class ActivityLogger extends SpatieActivityLogger
{
    /**
     * @param string $description
     *
     * @return null|ActivityContract
     */
    public function log(string $description): ?ActivityContract
    {
        if ($this->logStatus->disabled()) {
            return null;
        }

        if (session()->has('institution') && isset(session('institution')->codigo_institucion)) {
            $this->getActivity()->codigo_institucion = session('institution')->codigo_institucion;
        }

        return parent::log($description);
    }
}
