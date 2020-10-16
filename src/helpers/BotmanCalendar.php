<?php

namespace Uasoft\BotmanCalendar\helpers;

use Uasoft\BotmanCalendar\conversation\BotmanCalendarConversation;

class BotmanCalendar
{
    protected $calendar;

    public function __construct($msg, $callback = null)
    {
        $this->startConversation($msg, $callback);
    }

    public function startConversation($msg, $callback)
    {
        $botman = resolve('botman');
        $conversation = new BotmanCalendarConversation();
        $conversation->message = $msg;
        $conversation->callback = $callback;
        $botman->startConversation($conversation);
    }
}
