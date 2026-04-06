<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZohoSyncLog extends Model
{
    protected $fillable = [
        'direction', 'entity_type', 'local_id', 'remote_id', 'status', 'message', 'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}
