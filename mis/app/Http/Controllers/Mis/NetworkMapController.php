<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\VaEngagement;
use Illuminate\View\View;

/**
 * Presents the MIS as an interconnected module map (data flow / “network” view).
 * This is an operational diagram, not a machine-learning neural network.
 */
class NetworkMapController extends Controller
{
    public function __invoke(): View
    {
        $year = now()->year;

        return view('mis.network.index', [
            'stats' => [
                'leads' => Lead::count(),
                'clients' => Client::count(),
                'quotations' => Quotation::count(),
                'invoices' => Invoice::count(),
                'expenses' => Expense::count(),
                'bookings_ytd' => Booking::whereYear('starts_at', $year)->where('status', '!=', 'cancelled')->count(),
                'va_active' => VaEngagement::where('status', 'active')->count(),
            ],
        ]);
    }
}
