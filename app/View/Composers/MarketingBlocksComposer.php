<?php

namespace App\View\Composers;

use App\Models\ContentBlock;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MarketingBlocksComposer
{
    private static ?Collection $cache = null;

    public function compose(View $view): void
    {
        if (! $view->offsetExists('blocks')) {
            $view->with('blocks', $this->blocks());
        }
    }

    private function blocks(): Collection
    {
        if (self::$cache === null) {
            self::$cache = ContentBlock::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('key')
                ->get()
                ->keyBy('key');
        }

        return self::$cache;
    }
}
