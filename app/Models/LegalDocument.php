<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalDocument extends Model
{
    /** @var list<string> */
    public const STATUSES = ['draft', 'published'];

    protected $fillable = [
        'slug',
        'title',
        'category',
        'body_html',
        'effective_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'effective_at' => 'date',
        ];
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}

