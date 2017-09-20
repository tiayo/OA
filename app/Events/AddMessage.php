<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class AddMessage
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $type; //类型
    public $option; //操作
    public $data; //当前数据
    public $origin; //操作前数据

    public function __construct($type, $option, $data, $origin = [])
    {
        $this->type = $type;
        $this->option = $option;
        $this->data = $data;
        $this->origin = $origin;
    }
}
