<?php

namespace App\Traits;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\LogOptions;

trait AppLogsActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $options = LogOptions::defaults();
        
        if (property_exists($this, 'logAttributes')) {
            $options->logAttributes(static::$logAttributes);
        }
        
        if (property_exists($this, 'logName')) {
            $options->useLogName(static::$logName);
        }
        
        // Emulate the v3 getDescriptionForEvent behavior
        if (method_exists($this, 'getDescriptionForEvent')) {
            $options->setDescriptionForEvent(function(string $eventName) {
                return $this->getDescriptionForEvent($eventName);
            });
        }
        
        return $options;
    }
}
