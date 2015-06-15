<?php namespace App\Events;

abstract class Event
{
    public function __construct()
    {
        \Log::info('Event triggered ['.get_called_class().']');
    }
}
