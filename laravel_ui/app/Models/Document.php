<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = ['title', 'file_path', 'type', 'description', 'company_id'];

    protected static function booted(): void
    {
        // Automatically filter all queries by the authenticated user's company.
        static::addGlobalScope(new CompanyScope);

        // Automatically stamp company_id on every new record.
        static::creating(function (self $model): void {
            if (! $model->company_id && auth()->check()) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
