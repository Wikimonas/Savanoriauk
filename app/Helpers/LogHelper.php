<?php
namespace App\Helpers;

use App\Models\ActionLog;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    public static function logAction(string $action, $model = null, array $changes = [])
    {
        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'changes' => $changes,
            'ip_address' => request()->ip(),
        ]);
    }
}
