<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\PricingPlan;
use Illuminate\View\View;

class VirtualAssistantController extends Controller
{
    public function __invoke(): View
    {
        $pricingPlans = PricingPlan::query()
            ->active()
            ->ordered()
            ->forVa()
            ->with('features')
            ->get();

        return view('marketing.virtual-assistant', [
            'settings' => CompanySetting::current(),
            'pricingPlans' => $pricingPlans,
        ]);
    }
}
