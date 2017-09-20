<?php

namespace App\Listeners;

use App\Events\AddMessage;
use App\Services\Admin\MessageService;

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
