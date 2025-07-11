<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DownloadCode extends Model
{
    protected $fillable = [
        'code',
        'name',
        'is_active',
        'expires_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime'
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public static function findValidCode(string $code): ?self
    {
        return self::where('code', $code)->first()?->isValid() ? 
            self::where('code', $code)->first() : null;
    }
}
