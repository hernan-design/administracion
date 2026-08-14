<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Models\Seguridad\Usuario;

class CheckUser extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $cod;    
    public $name;
    public $nickname;
    public $avatar;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Usuario $user)
    {
        $this->cod = $user->codigo_usuario;
        $this->name = $user->getName();
        $this->nickname = $user->nickname_usuario;
        $this->avatar = $user->imagen_usuario;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['user-channel'];
    }
}
