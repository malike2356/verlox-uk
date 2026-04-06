<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'date',
        'category',
        'vendor',
        'description',
        'amount_pence',
        'currency',
        'status',
        'reference',
        'notes',
        'zoho_expense_id',
    ];

    protected $casts = [
        'date' => 'date',
        'amount_pence' => 'integer',
    ];

    public const CATEGORIES = [
        'software' => 'Software & SaaS',
        'hosting' => 'Hosting & Infrastructure',
        'office' => 'Office & Admin',
        'marketing' => 'Marketing & Advertising',
        'professional_services' => 'Professional Services',
        'travel' => 'Travel & Transport',
        'equipment' => 'Equipment & Hardware',
        'subcontractors' => 'Subcontractors',
        'bank_charges' => 'Bank Charges',
        'other' => 'Other',
    ];

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    public function getAmountAttribute(): float
    {
        return $this->amount_pence / 100;
    }

    public function isSyncedToZoho(): bool
    {
        return filled($this->zoho_expense_id);
    }
}
