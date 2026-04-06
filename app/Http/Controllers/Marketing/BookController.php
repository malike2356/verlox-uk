<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\PricingPlan;
use Illuminate\View\View;

class BookController extends Controller
{
    public function __invoke(): View
    {
        $pricingPlans = PricingPlan::query()
            ->active()
            ->ordered()
            ->forBook()
            ->with('features')
            ->get();

        return view('marketing.book', [
            'settings' => CompanySetting::current(),
            'misBase' => rtrim(url('/'), '/'),
            'pricingPlans' => $pricingPlans,
        ]);
    }
}
