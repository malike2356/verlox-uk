<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function record(
        Model $model,
        string $action,
        ?array $oldValues,
        ?array $newValues,
        ?Request $request = null
    ): void {
        $req = $request ?? request();
        AuditLog::query()->create([
            'user_id' => Auth::id(),
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $req?->ip(),
            'user_agent' => $req ? substr((string) $req->userAgent(), 0, 512) : null,
        ]);
    }
}
