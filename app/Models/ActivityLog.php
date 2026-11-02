<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    /** Set to false to silence logging (used while seeding). */
    public static bool $enabled = true;

    protected $fillable = ['user_id', 'username', 'action', 'description', 'ip_address'];

    public static function record(string $action, string $description, ?User $user = null): void
    {
        if (! static::$enabled) {
            return;
        }

        $user ??= auth()->user();

        static::create([
            'user_id'     => $user?->id,
            'username'    => $user?->name ?? 'system',
            'action'      => $action,
            'description' => Str::limit($description, 250, ''),
            'ip_address'  => request()->ip(),
        ]);
    }

    public function getActionClassAttribute(): string
    {
        return match ($this->action) {
            'create'                  => 'success',
            'update'                  => 'primary',
            'delete', 'locked'        => 'danger',
            'failed_login'            => 'warning',
            'login', 'logout'         => 'info',
            default                   => 'secondary',
        };
    }
}
