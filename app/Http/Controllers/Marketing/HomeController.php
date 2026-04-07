<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\PricingPlan;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $settings = CompanySetting::current();

        $pricingPlans = PricingPlan::query()
            ->active()
            ->ordered()
            ->forHome()
            ->with('features')
            ->get();

        return view('marketing.home', compact('settings', 'pricingPlans'));
    }
}
