<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CompanySetting;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\VaEngagement;
use App\Services\ZohoBooksClient;
use Carbon\Carbon;
use Illuminate\View\View;

class FinanceDashboardController extends Controller
{
    public function __invoke(ZohoBooksClient $zoho): View
    {
        $year = now()->year;

        // ── Year-to-date KPIs ────────────────────────────────────────────
        $revenueYtd = Invoice::whereYear('issued_at', $year)->where('status', 'paid')->sum('total_pence');
        $invoicedYtd = Invoice::whereYear('issued_at', $year)->whereIn('status', ['sent', 'paid'])->sum('total_pence');
        $outstanding = Invoice::where('status', 'sent')->sum('total_pence');
        $expensesYtd = Expense::whereYear('date', $year)->sum('amount_pence');
        $netProfit = $revenueYtd - $expensesYtd;
        $taxEstimate = $netProfit > 0 ? (int) round($netProfit * 0.19) : 0; // Corp tax at 19%

        // ── Monthly P&L (all 12 months of the selected year) ─────────────
        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthly[] = [
                'label' => Carbon::create($year, $m)->format('M'),
                'income' => round(Invoice::whereYear('issued_at', $year)->whereMonth('issued_at', $m)->where('status', 'paid')->sum('total_pence') / 100, 2),
                'expenses' => round(Expense::whereYear('date', $year)->whereMonth('date', $m)->sum('amount_pence') / 100, 2),
            ];
        }

        // ── Expense breakdown by category ────────────────────────────────
        $expenseByCategory = Expense::whereYear('date', $year)
            ->selectRaw('category, SUM(amount_pence) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // ── Recent activity ──────────────────────────────────────────────
        $recentInvoices = Invoice::with('client')->latest('issued_at')->limit(6)->get();
        $recentExpenses = Expense::latest('date')->limit(6)->get();

        $paidBase = Invoice::query()
            ->whereYear('issued_at', $year)
            ->where('status', 'paid');
        $revenueFromCatalogue = (clone $paidBase)->whereNotNull('offering_id')->sum('total_pence');
        $revenueFromQuotations = (clone $paidBase)->whereNull('offering_id')->whereNotNull('quotation_id')->sum('total_pence');
        $revenueAdHoc = (clone $paidBase)->whereNull('offering_id')->whereNull('quotation_id')->sum('total_pence');

        $vaActiveCount = VaEngagement::where('status', 'active')->count();
        $vaMrrPence = (int) VaEngagement::where('status', 'active')->get()->sum(
            fn (VaEngagement $e) => (int) round((float) ($e->client_rate_monthly_gbp ?? 0) * 100)
        );

        $bookingsYtd = Booking::query()
            ->whereYear('starts_at', $year)
            ->where('status', '!=', 'cancelled')
            ->count();

        $settings = CompanySetting::current();
        $zohoLinked = $zoho->isConfigured();
        $zohoInvoiceAuto = $zohoLinked && $settings->zoho_auto_sync_invoices;
        $zohoExpenseAuto = $zohoLinked && $settings->zoho_auto_sync_expenses;

        return view('mis.finance.dashboard', compact(
            'year',
            'revenueYtd', 'invoicedYtd', 'outstanding',
            'expensesYtd', 'netProfit', 'taxEstimate',
            'monthly', 'expenseByCategory',
            'recentInvoices', 'recentExpenses',
            'revenueFromCatalogue', 'revenueFromQuotations', 'revenueAdHoc',
            'vaActiveCount', 'vaMrrPence',
            'bookingsYtd',
            'zohoLinked', 'zohoInvoiceAuto', 'zohoExpenseAuto',
        ));
    }
}
