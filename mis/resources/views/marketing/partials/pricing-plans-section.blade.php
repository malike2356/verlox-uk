@if($pricingPlans->isNotEmpty())
<section class="section section--pricing {{ $sectionClass ?? '' }}" id="{{ $sectionId ?? 'pricing' }}">
    <div class="container">
        <header class="section__head section__head--left reveal">
            <p class="section__eyebrow">{{ $eyebrow ?? 'Plans' }}</p>
            <h2 class="section__title">{{ $title ?? 'Straightforward pricing' }}</h2>
            @if(!empty($subtitle))
                <p class="section__subtitle">{{ $subtitle }}</p>
            @endif
        </header>

        <div class="pricing-grid reveal">
            @foreach($pricingPlans as $plan)
                @php($ctaUrl = $plan->resolveCtaUrl())
                <article class="pricing-card {{ $plan->is_featured ? 'pricing-card--featured' : '' }}">
                    @if($plan->is_featured)
                        <span class="pricing-card__badge">{{ __('Popular') }}</span>
                    @endif
                    <h3 class="pricing-card__name">{{ $plan->name }}</h3>
                    @if($plan->tagline)
                        <p class="pricing-card__tagline">{{ $plan->tagline }}</p>
                    @endif
                    <p class="pricing-card__price">{{ $plan->formattedPrice() }}</p>
                    @if($plan->formattedCompareAt())
                        <p class="pricing-card__compare">{{ __('Was') }} {{ $plan->formattedCompareAt() }}</p>
                    @endif
                    @if($plan->description)
                        <p class="pricing-card__desc">{{ $plan->description }}</p>
                    @endif
                    @if($plan->features->isNotEmpty())
                        <ul class="pricing-card__features">
                            @foreach($plan->features as $f)
                                <li class="{{ $f->is_included ? '' : 'pricing-card__feature--muted' }}">
                                    <span class="pricing-card__tick" aria-hidden="true">{{ $f->is_included ? '✓' : '×' }}</span>
                                    {{ $f->label }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    @if($ctaUrl)
                        <a class="btn {{ $plan->is_featured ? 'btn--primary' : 'btn--ghost' }} pricing-card__cta" href="{{ $ctaUrl }}">{{ $plan->defaultCtaLabel() }}</a>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
