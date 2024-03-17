<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TaskUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    public $task;
    public function __construct($user, $task)
    {
        $this->user = $user;
        $this->task = $task;
    }

    public function broadcastWith()
    {
        return ['message' => 'Task has been updated!', 'task' => $this->task];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('tasks.'.$this->user->id);
    }
}
