<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = ['client_id', 'title', 'file_path', 'mime', 'size_bytes'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
