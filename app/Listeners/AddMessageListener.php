<?php

namespace App\Listeners;

use App\Events\AddMessage;
use App\Services\Admin\MessageService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddMessageListener
{
    protected $message;

    public function __construct(MessageService $message)
    {
        $this->message = $message;
    }


    public function handle(AddMessage $event)
    {
        return $this->message->create($event);
    }
}
