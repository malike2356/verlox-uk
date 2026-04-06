<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

class PricingPlan extends Model
{
    public const BILLING_PERIODS = [
        'one_off' => 'One-off',
        'monthly' => 'Per month',
        'annual' => 'Per year',
        'per_session' => 'Per session',
        'custom' => 'Custom / contact',
    ];

    /** @var list<string> */
    public const ALLOWED_CTA_ROUTES = [
        'marketing.home',
        'marketing.book',
        'marketing.virtual-assistant',
    ];

    protected $fillable = [
        'name', 'slug', 'tagline', 'description',
        'price_pence', 'compare_at_pence', 'currency', 'billing_period',
        'sessions_included', 'price_display_override',
        'cta_label', 'cta_route', 'cta_url', 'offering_id',
        'show_on_home', 'show_on_book', 'show_on_va',
        'display_order', 'is_active', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'show_on_home' => 'boolean',
            'show_on_book' => 'boolean',
            'show_on_va' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function features(): HasMany
    {
        return $this->hasMany(PricingPlanFeature::class)->orderBy('sort_order')->orderBy('id');
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('display_order')->orderBy('id');
    }

    public function scopeForHome(Builder $q): Builder
    {
        return $q->where('show_on_home', true);
    }

    public function scopeForBook(Builder $q): Builder
    {
        return $q->where('show_on_book', true);
    }

    public function scopeForVa(Builder $q): Builder
    {
        return $q->where('show_on_va', true);
    }

    public function formattedPrice(): string
    {
        if ($this->price_display_override) {
            return $this->price_display_override;
        }
        if ($this->price_pence === null) {
            return __('Contact us');
        }
        $sym = $this->currency === 'GBP' ? '£' : $this->currency.' ';
        $amount = number_format($this->price_pence / 100, 2);
        $base = $sym.$amount;
        if ($this->billing_period === 'one_off' && $this->sessions_included && $this->sessions_included > 1) {
            return $base.' · '.$this->sessions_included.' '.__('sessions');
        }

        return match ($this->billing_period) {
            'monthly' => $base.__('/mo'),
            'annual' => $base.__('/yr'),
            'per_session' => $base.' '.__('per session'),
            'custom' => $base,
            default => $base,
        };
    }

    public function resolveCtaUrl(): ?string
    {
        if ($this->cta_url) {
            return $this->cta_url;
        }
        if ($this->offering_id) {
            $off = $this->relationLoaded('offering') ? $this->offering : Offering::query()->find($this->offering_id);
            if ($off) {
                return route('checkout.show', $off);
            }
        }
        if ($this->cta_route && in_array($this->cta_route, self::ALLOWED_CTA_ROUTES, true) && Route::has($this->cta_route)) {
            return route($this->cta_route);
        }

        return null;
    }

    public function defaultCtaLabel(): string
    {
        return $this->cta_label ?: __('Get started');
    }

    public function formattedCompareAt(): ?string
    {
        if ($this->compare_at_pence === null) {
            return null;
        }
        $sym = $this->currency === 'GBP' ? '£' : $this->currency.' ';

        return $sym.number_format($this->compare_at_pence / 100, 2);
    }
}
