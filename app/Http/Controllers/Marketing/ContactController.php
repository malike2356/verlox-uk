<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Offering;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __invoke(): View
    {
        $settings = CompanySetting::current();
        $offerings = Offering::query()->where('is_active', true)->orderBy('display_order')->orderBy('name')->get();

        return view('marketing.contact', compact('settings', 'offerings'));
    }
}

