<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    protected $fillable = ['name', 'logo', 'theme_color'];

    // ── Relationships ────────────────────────────────────────────────

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /** The one admin that belongs to this company. */
    public function admin(): HasOne
    {
        return $this->hasOne(User::class)->where('role', 'admin');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function ragHistories(): HasMany
    {
        return $this->hasMany(RagHistory::class);
    }

    // ── Accessors (keep view variable names unchanged) ───────────────

    /** Alias so views can use $siteSetting->company_name. */
    public function getCompanyNameAttribute(): ?string
    {
        return $this->name;
    }

    /** Alias so views can use $siteSetting->company_logo. */
    public function getCompanyLogoAttribute(): ?string
    {
        return $this->logo;
    }
}
