<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    protected $fillable = ['key', 'title', 'type', 'body', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public const TYPES = [
        'text'      => 'Single line text',
        'textarea'  => 'Plain text (multi-line)',
        'html'      => 'HTML / Rich content (WYSIWYG)',
        'markdown'  => 'Markdown',
        'image_url' => 'Image URL',
    ];

    /** Derive a human-readable section name from the key prefix */
    public function getSectionAttribute(): string
    {
        $parts = explode('_', $this->key);

        return ucwords(str_replace('_', ' ', $parts[0]));
    }
}
