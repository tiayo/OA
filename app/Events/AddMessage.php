<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AddMessage
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $type;
    public $option;
    public $data;
    public $origin;
    public $id;

    public function __construct($type, $option, $data, $origin = [], $id = null)
    {
        $this->type = $type;
        $this->option = $option;
        $this->data = $data;
        $this->origin = $origin;
        $this->id = $id;
    }
}
