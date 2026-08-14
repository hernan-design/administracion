<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Notification extends Event implements ShouldBroadcast
{
	use SerializesModels;

    public $title;
    public $content;
    public $institution;
    public $type;
    public $image;
    public $url;
    public $receivedBy;
    public $deliveredBy;
    public $module;
    public $resource;
    public $isGlobal;

    /**
     * Create a new event instance.
     * Type 1 - General, 2 - Solicitud contacto
     * @return void
     */
    public function __construct(Array $data)
    {
        // Default values
        $this->institution = ( isset($data['codigo_institucion']) && !empty($data['codigo_institucion']) ) ? $data['codigo_institucion'] : session('institution')->codigo_institucion;
        $this->image = ( isset($data['logo_institucion']) && !empty($data['logo_institucion']) ) ? $data['logo_institucion'] : session('institution')->logo_institucion;
        $this->type = 1;
        $this->deliveredBy = ( isset($data['codigo_usuario']) && !empty($data['codigo_usuario']) ) ? $data['codigo_usuario'] : \Auth::user()->codigo_usuario;
        $this->isGlobal = false;

        // Set parameters required
        $this->title = $data['title'];
        $this->content = $data['content'];

        // Set image if exist
        if(isset($data['image'])) {
            $this->image = $data['image'];
        }

        // Set url if exist
        if(isset($data['url'])) {
            $this->url = $data['url'];
        }

        // Set type if exist
        if(isset($data['type'])) {
            $this->type = $data['type'];
        }

        // Set receivedBy if exist
        if(isset($data['receivedBy'])) {
            $this->receivedBy = $data['receivedBy'];
        }

        // Set module if exist
        if(isset($data['module'])) {
            $this->module = $data['module'];
        }

        // Set resource if exist
        if(isset($data['resource'])) {
            $this->resource = $data['resource'];
        }

        if(isset($data['isGlobal'])) {
            $this->isGlobal = $data['isGlobal'];
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
		return ['notification-channel'];
    }
}
