<?php

namespace App\Events;

use App\Models\AuditLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class AuditLogCreated implements ShouldBroadcast
{
    use SerializesModels;

    public $log;

    public function __construct(AuditLog $log)
    {
        /*
        =====================================
        🔥 KIRIM DATA MINIMAL (RINGAN)
        =====================================
        */
        $this->log = [
            'id' => $log->id,
            'description' => $log->description,
            'model' => $log->model,
            'status' => $log->status,
            'created_at' => $log->created_at->diffForHumans(),
            'user' => [
                'name' => $log->user->name ?? 'System'
            ]
        ];
    }

    public function broadcastOn()
    {
        return new Channel('audit-log');
    }

    public function broadcastAs()
    {
        return 'new-log';
    }
}