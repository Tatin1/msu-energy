<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SystemLogRecorded implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Collection ready for the history/system tables.
     */
    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('system.logs');
    }

    public function broadcastAs(): string
    {
        return 'SystemLogRecorded';
    }
}
