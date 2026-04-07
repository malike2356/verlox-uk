<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LeadCaptureController;
use App\Http\Controllers\Marketing\BookController;
use App\Http\Controllers\Marketing\ContactController;
use App\Http\Controllers\Marketing\HomeController;
use App\Http\Controllers\Marketing\VirtualAssistantController;
use App\Http\Controllers\Mis\BookingController as MisBookingController;
use App\Http\Controllers\Mis\ClientController as MisClientController;
use App\Http\Controllers\Mis\ContentBlockController;
use App\Http\Controllers\Mis\ContractController as MisContractController;
use App\Http\Controllers\Mis\ContractTemplateController;
use App\Http\Controllers\Mis\ConversationController;
use App\Http\Controllers\Mis\DashboardController as MisDashboardController;
use App\Http\Controllers\Mis\DataExportController;
use App\Http\Controllers\Mis\DocumentController as MisDocumentController;
use App\Http\Controllers\Mis\EventTypeController;
use App\Http\Controllers\Mis\ExpenseController;
use App\Http\Controllers\Mis\FinanceDashboardController;
use App\Http\Controllers\Mis\GoogleCalendarController;
use App\Http\Controllers\Mis\HelpController;
use App\Http\Controllers\Mis\InvoiceController as MisInvoiceController;
use App\Http\Controllers\Mis\LegalDocumentController as MisLegalDocumentController;
use App\Http\Controllers\Mis\LeadActivityController;
use App\Http\Controllers\Mis\LeadController as MisLeadController;
use App\Http\Controllers\Mis\LeadConvertController;
use App\Http\Controllers\Mis\NetworkMapController;
use App\Http\Controllers\Mis\OfferingController as MisOfferingController;
use App\Http\Controllers\Mis\OfferingTypeController as MisOfferingTypeController;
use App\Http\Controllers\Mis\PipelineBoardController;
use App\Http\Controllers\Mis\PipelineStageController;
use App\Http\Controllers\Mis\PricingPlanController;
use App\Http\Controllers\Mis\QuotationController as MisQuotationController;
use App\Http\Controllers\Mis\ReceivablesController;
use App\Http\Controllers\Mis\SettingsController as MisSettingsController;
use App\Http\Controllers\Mis\SmartSearchController;
use App\Http\Controllers\Mis\UserController;
use App\Http\Controllers\Mis\Va\AssistantController as VaAssistantController;
use App\Http\Controllers\Mis\Va\ClientAccountController as VaClientAccountController;
use App\Http\Controllers\Mis\Va\CommunicationLogController as VaCommunicationLogController;
use App\Http\Controllers\Mis\Va\DashboardController as VaModuleDashboardController;
use App\Http\Controllers\Mis\Va\EngagementController as VaEngagementController;
use App\Http\Controllers\Mis\Va\TimeLogController as VaTimeLogController;
use App\Http\Controllers\Mis\ZohoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicLegalController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\PublicContentBlockController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('marketing.home');
Route::get('/virtual-assistant', VirtualAssistantController::class)->name('marketing.virtual-assistant');
Route::get('/book', BookController::class)->name('marketing.book');
Route::get('/contact', ContactController::class)->name('marketing.contact');
Route::post('/leads', [LeadCaptureController::class, 'store'])->name('leads.store');
Route::get('/legal/{slug}', [PublicLegalController::class, 'show'])->name('legal.show');

Route::get('/checkout/offering/{offering}', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout/offering/{offering}', [CheckoutController::class, 'start'])->name('checkout.start');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('/api/public/content-blocks', PublicContentBlockController::class)->name('public.content-blocks');
Route::get('/api/public/event-types', [PublicBookingController::class, 'eventTypes'])->name('public.event-types');
Route::get('/api/public/booking-questions', [PublicBookingController::class, 'questions'])->name('public.booking.questions');
Route::get('/api/public/booking-slots', [PublicBookingController::class, 'slots'])->name('public.booking.slots');
Route::get('/api/public/booking-calendar', [PublicBookingController::class, 'calendar'])->name('public.booking.calendar');
Route::post('/api/public/bookings', [PublicBookingController::class, 'store'])->name('public.bookings.store');
Route::get('/embed/booking', [PublicBookingController::class, 'embed'])->name('embed.booking');
Route::get('/bookings/{booking}/manage/{token}', [PublicBookingController::class, 'manage'])->name('public.booking.manage');
Route::post('/bookings/{booking}/manage/{token}/cancel', [PublicBookingController::class, 'cancel'])->name('public.booking.cancel');
Route::get('/bookings/{booking}/reschedule/{token}', [PublicBookingController::class, 'rescheduleForm'])->name('public.booking.reschedule.form');
Route::post('/bookings/{booking}/reschedule/{token}', [PublicBookingController::class, 'reschedule'])->name('public.booking.reschedule');
Route::get('/bookings/{booking}/ics', [PublicBookingController::class, 'ics'])
    ->middleware('signed')
    ->name('public.booking.ics');

Route::post('/webhooks/stripe', StripeWebhookController::class)->name('webhooks.stripe');

Route::get('/dashboard', function () {
    if (auth()->user()?->canAccessMis()) {
        return redirect()->route('mis.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/sessions/logout-others', [ProfileController::class, 'logoutOtherSessions'])
        ->name('profile.sessions.logout-others');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'mis.access'])->prefix('mis')->name('mis.')->scopeBindings()->group(function () {
    Route::get('/', MisDashboardController::class)->name('dashboard');
    Route::get('/help', HelpController::class)->name('help.index');
    Route::get('/network', NetworkMapController::class)->name('network.index');
    Route::get('/search', SmartSearchController::class)->name('search');

    Route::prefix('va')->name('va.')->group(function () {
        Route::get('/', VaModuleDashboardController::class)->name('dashboard');

        Route::get('client-accounts/{va_client_account}/engagements/create', [VaEngagementController::class, 'create'])
            ->name('client-accounts.engagements.create');
        Route::post('client-accounts/{va_client_account}/engagements', [VaEngagementController::class, 'store'])
            ->name('client-accounts.engagements.store');
        Route::post('client-accounts/{va_client_account}/communications', [VaCommunicationLogController::class, 'store'])
            ->name('client-accounts.communications.store');

        Route::get('engagements/{va_engagement}/edit', [VaEngagementController::class, 'edit'])->name('engagements.edit');
        Route::patch('engagements/{va_engagement}', [VaEngagementController::class, 'update'])->name('engagements.update');
        Route::delete('engagements/{va_engagement}', [VaEngagementController::class, 'destroy'])->name('engagements.destroy');

        Route::get('time-logs', [VaTimeLogController::class, 'index'])->name('time-logs.index');
        Route::get('time-logs/create', [VaTimeLogController::class, 'create'])->name('time-logs.create');
        Route::post('time-logs', [VaTimeLogController::class, 'store'])->name('time-logs.store');
        Route::patch('time-logs/{va_time_log}/approve', [VaTimeLogController::class, 'approve'])->name('time-logs.approve');

        Route::resource('assistants', VaAssistantController::class)
            ->parameters(['assistants' => 'va_assistant'])
            ->except(['show']);
        Route::resource('client-accounts', VaClientAccountController::class)
            ->parameters(['client-accounts' => 'va_client_account']);
    });

    Route::middleware(['mis.super'])->group(function () {
        Route::get('/contract-templates', [ContractTemplateController::class, 'index'])->name('contract-templates.index');
        Route::get('/contract-templates/create', [ContractTemplateController::class, 'create'])->name('contract-templates.create');
        Route::post('/contract-templates', [ContractTemplateController::class, 'store'])->name('contract-templates.store');
        Route::get('/contract-templates/{contractTemplate}/edit', [ContractTemplateController::class, 'edit'])->name('contract-templates.edit');
        Route::patch('/contract-templates/{contractTemplate}', [ContractTemplateController::class, 'update'])->name('contract-templates.update');
        Route::delete('/contract-templates/{contractTemplate}', [ContractTemplateController::class, 'destroy'])->name('contract-templates.destroy');

        Route::get('/legal-documents', [MisLegalDocumentController::class, 'index'])->name('legal-documents.index');
        Route::get('/legal-documents/create', [MisLegalDocumentController::class, 'create'])->name('legal-documents.create');
        Route::post('/legal-documents', [MisLegalDocumentController::class, 'store'])->name('legal-documents.store');
        Route::get('/legal-documents/{legalDocument}/edit', [MisLegalDocumentController::class, 'edit'])->name('legal-documents.edit');
        Route::patch('/legal-documents/{legalDocument}', [MisLegalDocumentController::class, 'update'])->name('legal-documents.update');
        Route::delete('/legal-documents/{legalDocument}', [MisLegalDocumentController::class, 'destroy'])->name('legal-documents.destroy');
        Route::get('/legal-documents/{legalDocument}/download', [MisLegalDocumentController::class, 'downloadHtml'])->name('legal-documents.download');
        Route::post('/legal-documents/{legalDocument}/create-document', [MisLegalDocumentController::class, 'createDocumentRecord'])->name('legal-documents.create-document');

        Route::get('/event-types', [EventTypeController::class, 'index'])->name('event-types.index');
        Route::post('/event-types', [EventTypeController::class, 'store'])->name('event-types.store');
        Route::patch('/event-types/{eventType}', [EventTypeController::class, 'update'])->name('event-types.update');
        Route::delete('/event-types/{eventType}', [EventTypeController::class, 'destroy'])->name('event-types.destroy');
        Route::post('/event-types/{eventType}/questions', [EventTypeController::class, 'storeQuestion'])->name('event-types.questions.store');
        Route::delete('/event-types/{eventType}/questions/{question}', [EventTypeController::class, 'destroyQuestion'])->name('event-types.questions.destroy');

        Route::get('/settings/google-calendar/connect', [GoogleCalendarController::class, 'redirect'])->name('google-calendar.connect');
        Route::get('/settings/google-calendar/callback', [GoogleCalendarController::class, 'callback'])->name('google-calendar.callback');
        Route::delete('/settings/google-calendar', [GoogleCalendarController::class, 'disconnect'])->name('google-calendar.disconnect');

        Route::get('/bookings-meta/availability', [MisBookingController::class, 'availability'])->name('bookings.availability');
        Route::post('/bookings-meta/availability', [MisBookingController::class, 'storeRule'])->name('bookings.availability.store');
        Route::patch('/bookings-meta/availability/{bookingAvailabilityRule}', [MisBookingController::class, 'updateRule'])->name('bookings.availability.update');
        Route::patch('/bookings-meta/availability/{bookingAvailabilityRule}/weekday', [MisBookingController::class, 'moveRule'])->name('bookings.availability.move');
        Route::post('/bookings-meta/availability/{bookingAvailabilityRule}/duplicate', [MisBookingController::class, 'duplicateRule'])->name('bookings.availability.duplicate');
        Route::delete('/bookings-meta/availability/{bookingAvailabilityRule}', [MisBookingController::class, 'destroyRule'])->name('bookings.availability.destroy');
        Route::post('/bookings-meta/overrides', [MisBookingController::class, 'storeOverride'])->name('bookings.overrides.store');
        Route::patch('/bookings-meta/overrides/{bookingDateOverride}', [MisBookingController::class, 'updateOverride'])->name('bookings.overrides.update');
        Route::delete('/bookings-meta/overrides/{bookingDateOverride}', [MisBookingController::class, 'destroyOverride'])->name('bookings.overrides.destroy');

        Route::delete('/bookings/{booking}', [MisBookingController::class, 'destroy'])->name('bookings.destroy');

        Route::get('/offering-types', [MisOfferingTypeController::class, 'index'])->name('offering-types.index');
        Route::get('/offering-types/create', [MisOfferingTypeController::class, 'create'])->name('offering-types.create');
        Route::post('/offering-types', [MisOfferingTypeController::class, 'store'])->name('offering-types.store');
        Route::get('/offering-types/{offeringType}/edit', [MisOfferingTypeController::class, 'edit'])->name('offering-types.edit');
        Route::patch('/offering-types/{offeringType}', [MisOfferingTypeController::class, 'update'])->name('offering-types.update');
        Route::delete('/offering-types/{offeringType}', [MisOfferingTypeController::class, 'destroy'])->name('offering-types.destroy');

        Route::get('/offerings', [MisOfferingController::class, 'index'])->name('offerings.index');
        Route::get('/offerings/create', [MisOfferingController::class, 'create'])->name('offerings.create');
        Route::post('/offerings', [MisOfferingController::class, 'store'])->name('offerings.store');
        Route::get('/offerings/{offering}/edit', [MisOfferingController::class, 'edit'])->name('offerings.edit');
        Route::patch('/offerings/{offering}', [MisOfferingController::class, 'update'])->name('offerings.update');
        Route::delete('/offerings/{offering}', [MisOfferingController::class, 'destroy'])->name('offerings.destroy');

        Route::get('/pricing-plans', [PricingPlanController::class, 'index'])->name('pricing-plans.index');
        Route::get('/pricing-plans/create', [PricingPlanController::class, 'create'])->name('pricing-plans.create');
        Route::post('/pricing-plans', [PricingPlanController::class, 'store'])->name('pricing-plans.store');
        Route::get('/pricing-plans/{pricing_plan}/edit', [PricingPlanController::class, 'edit'])->name('pricing-plans.edit');
        Route::patch('/pricing-plans/{pricing_plan}', [PricingPlanController::class, 'update'])->name('pricing-plans.update');
        Route::delete('/pricing-plans/{pricing_plan}', [PricingPlanController::class, 'destroy'])->name('pricing-plans.destroy');

        Route::get('/pipeline/stages', [PipelineStageController::class, 'index'])->name('pipeline.stages.index');
        Route::get('/pipeline/stages/create', [PipelineStageController::class, 'create'])->name('pipeline.stages.create');
        Route::post('/pipeline/stages', [PipelineStageController::class, 'store'])->name('pipeline.stages.store');
        Route::get('/pipeline/stages/{pipelineStage}/edit', [PipelineStageController::class, 'edit'])->name('pipeline.stages.edit');
        Route::patch('/pipeline/stages/{pipelineStage}', [PipelineStageController::class, 'update'])->name('pipeline.stages.update');
        Route::delete('/pipeline/stages/{pipelineStage}', [PipelineStageController::class, 'destroy'])->name('pipeline.stages.destroy');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/content-blocks', [ContentBlockController::class, 'index'])->name('content-blocks.index');
        Route::post('/content-blocks', [ContentBlockController::class, 'store'])->name('content-blocks.store');
        Route::post('/content-blocks/upload-image', [ContentBlockController::class, 'uploadImage'])->name('content-blocks.upload-image');
        Route::get('/content-blocks/{contentBlock}/edit', [ContentBlockController::class, 'edit'])->name('content-blocks.edit');
        Route::post('/content-blocks/{contentBlock}/duplicate', [ContentBlockController::class, 'duplicate'])->name('content-blocks.duplicate');
        Route::patch('/content-blocks/{contentBlock}', [ContentBlockController::class, 'update'])->name('content-blocks.update');
        Route::delete('/content-blocks/{contentBlock}', [ContentBlockController::class, 'destroy'])->name('content-blocks.destroy');

        Route::get('/settings', [MisSettingsController::class, 'edit'])->name('settings.edit');
        Route::patch('/settings', [MisSettingsController::class, 'update'])->name('settings.update');

        Route::post('/zoho/test', [ZohoController::class, 'test'])->name('zoho.test');
    });

    Route::get('/leads', [MisLeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/create', [MisLeadController::class, 'create'])->name('leads.create');
    Route::post('/leads', [MisLeadController::class, 'store'])->name('leads.store');
    Route::get('/leads/{lead}', [MisLeadController::class, 'show'])->name('leads.show');
    Route::patch('/leads/{lead}', [MisLeadController::class, 'update'])->name('leads.update');
    Route::delete('/leads/{lead}', [MisLeadController::class, 'destroy'])->name('leads.destroy');
    Route::post('/leads/{lead}/convert', LeadConvertController::class)->name('leads.convert');
    Route::post('/leads/{lead}/activities', [LeadActivityController::class, 'store'])->name('leads.activities.store');

    Route::get('/clients', [MisClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [MisClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [MisClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [MisClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [MisClientController::class, 'edit'])->name('clients.edit');
    Route::patch('/clients/{client}', [MisClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [MisClientController::class, 'destroy'])->name('clients.destroy');

    Route::get('/quotations', [MisQuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [MisQuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [MisQuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}/edit', [MisQuotationController::class, 'edit'])->name('quotations.edit');
    Route::patch('/quotations/{quotation}', [MisQuotationController::class, 'update'])->name('quotations.update');
    Route::delete('/quotations/{quotation}', [MisQuotationController::class, 'destroy'])->name('quotations.destroy');
    Route::get('/quotations/{quotation}', [MisQuotationController::class, 'show'])->name('quotations.show');
    Route::post('/quotations/{quotation}/lines', [MisQuotationController::class, 'addLine'])->name('quotations.lines.store');
    Route::delete('/quotations/{quotation}/lines/{line}', [MisQuotationController::class, 'destroyLine'])->name('quotations.lines.destroy');
    Route::patch('/quotations/{quotation}/status', [MisQuotationController::class, 'updateStatus'])->name('quotations.status');
    Route::post('/quotations/{quotation}/accept', [MisQuotationController::class, 'accept'])->name('quotations.accept');

    Route::get('/contracts', [MisContractController::class, 'index'])->name('contracts.index');
    Route::get('/contracts/{contract}', [MisContractController::class, 'show'])->name('contracts.show');
    Route::delete('/contracts/{contract}', [MisContractController::class, 'destroy'])->name('contracts.destroy');
    Route::patch('/contracts/{contract}/status', [MisContractController::class, 'updateStatus'])->name('contracts.status');

    Route::get('/finance', FinanceDashboardController::class)->name('finance.dashboard');
    Route::get('/finance/receivables', ReceivablesController::class)->name('finance.receivables');
    Route::get('/finance/expenses', [ExpenseController::class, 'index'])->name('finance.expenses.index');
    Route::post('/finance/expenses', [ExpenseController::class, 'store'])->name('finance.expenses.store');
    Route::patch('/finance/expenses/{expense}', [ExpenseController::class, 'update'])->name('finance.expenses.update');
    Route::delete('/finance/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('finance.expenses.destroy');
    Route::post('/finance/expenses/{expense}/sync-zoho', [ExpenseController::class, 'syncZoho'])->name('finance.expenses.sync-zoho');

    Route::get('/exports/leads', [DataExportController::class, 'leads'])->name('exports.leads');
    Route::get('/exports/invoices', [DataExportController::class, 'invoices'])->name('exports.invoices');
    Route::get('/exports/expenses', [DataExportController::class, 'expenses'])->name('exports.expenses');

    Route::get('/invoices', [MisInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [MisInvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [MisInvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}', [MisInvoiceController::class, 'show'])->name('invoices.show');
    Route::delete('/invoices/{invoice}', [MisInvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::patch('/invoices/{invoice}/status', [MisInvoiceController::class, 'updateStatus'])->name('invoices.status');
    Route::post('/invoices/{invoice}/reminder', [MisInvoiceController::class, 'recordReminder'])->name('invoices.reminder');
    Route::post('/quotations/{quotation}/invoice', [MisInvoiceController::class, 'fromQuotation'])->name('invoices.from-quotation');
    Route::post('/invoices/{invoice}/stripe-checkout', [MisInvoiceController::class, 'stripeCheckout'])->name('invoices.stripe-checkout');
    Route::post('/invoices/{invoice}/sync-zoho', [MisInvoiceController::class, 'syncZoho'])->name('invoices.sync-zoho');

    Route::get('/bookings', [MisBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/calendar.json', [MisBookingController::class, 'calendarFeed'])->name('bookings.calendar');
    Route::get('/bookings/{booking}', [MisBookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/status', [MisBookingController::class, 'updateStatus'])->name('bookings.status');

    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/create', [ConversationController::class, 'create'])->name('conversations.create');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy'])->name('conversations.destroy');
    Route::post('/conversations/{conversation}/reply', [ConversationController::class, 'reply'])->name('conversations.reply');

    Route::get('/documents', [MisDocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [MisDocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [MisDocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/download', [MisDocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [MisDocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('/pipeline', [PipelineBoardController::class, 'index'])->name('pipeline.index');
    Route::patch('/leads/{lead}/stage', [MisLeadController::class, 'updateStage'])->name('leads.update-stage');

    Route::get('/zoho', [ZohoController::class, 'index'])->name('zoho.index');
});

require __DIR__.'/auth.php';
