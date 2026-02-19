<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    public function log(string $action, string $model, ?int $modelId = null, array $payload = []): AuditLog
    {
        return AuditLog::create([
            'action'   => $action,
            'model'    => $model,
            'model_id' => $modelId,
            'payload'  => $payload,
            'actor_id' => Auth::id(),
        ]);
    }
}
