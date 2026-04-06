<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PipelineStage extends Model
{
    protected $fillable = ['name', 'sort_order', 'color_hex'];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
