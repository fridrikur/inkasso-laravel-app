<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Kreditorer;
use App\Models\Debitorer;
use App\Models\Tokens;

class Sager extends Model
{
    use SoftDeletes;

    protected $table = 'sagers';

    protected $fillable = [
        'sagsnr',
        'afsluttet',
        'faktureret',
        'betalt',
        'fakturadato',
        'modtaget',
        'senesterapport',
        'opgivet',
        'hovedstol',
        'renter',
        'gebyr',
        'ialt',
        'startgebyr',
        'restgaeld_dkg', // gammelt statistik felt
        'indbetalt',
        'n_mdlydelse',
        'stelnr',
        'aktiv',
        'fakturanr',
        'restgaeld_kreditor',
        'kort_bemaerkning',
        'kode',
        'dato',
    ];

    protected $casts = [
        'afsluttet' => 'datetime',
        'faktureret' => 'datetime',
        'betalt' => 'datetime',
        'fakturadato' => 'datetime',
        'modtaget' => 'datetime',
        'senesterapport' => 'datetime',
        'opgivet' => 'datetime',
        'dato' => 'datetime',
    ];

    protected static ?\Illuminate\Support\Collection $fieldSettingsCache = null;

    /* =========================================================================
     * RELATIONER
     * ========================================================================= */

    public function sagerkreditor()
    {
        return $this->belongsToMany(Kreditorer::class, 'sager_kreditor', 'sag_id', 'kreditor_id');
    }

    public function sagerdebitor()
    {
        return $this->belongsToMany(Debitorer::class, 'sager_debitor', 'sag_id', 'debitor_id');
    }

    public function sagersagsbehandler()
    {
        return $this->belongsToMany(Sagsbehandler::class, 'sager_sagsbehandler', 'sag_id', 'sagsbehandler_id');
    }

    public function sagerkonsulent()
    {
        return $this->belongsToMany(Konsulenter::class, 'sager_konsulent', 'sag_id', 'konsulent_id');
    }

    public function sagertokens()
    {
        return $this->belongsToMany(Tokens::class, 'sager_tokens', 'sag_id', 'token_id');
    }

    public function sagerStatus()
    {
        return $this->belongsToMany(Status::class, 'sager_status', 'sag_id', 'status_id');
    }

    public function sagerKtr()  
    {
        return $this->belongsToMany(ktr::class, 'sager_ktr', 'sag_id', 'ktr_id');
    }

    public function sagerBemaerkning()
    {
        return $this->belongsToMany(bemaerkning::class, 'sager_bemaerkning', 'sag_id', 'bemaerkning_id');
    }

    public function sagerAfslutning()
    {
        return $this->belongsToMany(afslutning::class, 'sager_afslutning', 'sag_id', 'afslutning_id');
    }

    public function sagerUdlaeg()
    {
        return $this->belongsToMany(udlaeg::class, 'sager_udlaeg', 'sag_id', 'udlaeg_id');
    }

    public function importSessions()
    {
        return $this->belongsToMany(
            ImportSession::class,
            'import_session_sager',
            'sag_id',
            'import_session_id'
        )->withTimestamps();
    }

    public function dialogs()
    {
        return $this->hasMany(Dialog::class, 'sag_id');
    }

    public function dokumenter()
    {
        return $this->hasMany(Dokument::class, 'sag_id');
    }

    public function activities()
    {
        return $this->hasMany(SagActivity::class, 'sag_id');
    }

    public function users()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'sag_user_status',
            'sag_id',
            'user_id'
        )
        ->withPivot('read_at')
        ->withTimestamps();
    }

    /* =========================================================================
     * FIELD SETTINGS & HELPERS
     * ========================================================================= */

    public static function getFieldSettings(): \Illuminate\Support\Collection
    {
        return static::$fieldSettingsCache ??=
            SagerFieldSetting::query()
                ->get()
                ->keyBy('field_name');
    }

    public static function alias(string $field): string
    {
        return static::getFieldSettings()[$field]?->alias
            ?? ucfirst(str_replace('_', ' ', $field));
    }

    public static function visibleFields(): array
    {
        return static::getFieldSettings()
            ->filter(fn ($setting) => $setting->visible)
            ->keys()
            ->toArray();
    }

    public static function requiredFields(): array
    {
        return static::getFieldSettings()
            ->filter(fn ($setting) => $setting->required)
            ->keys()
            ->toArray();
    }

    public static function getRequiredFieldsForRole(?string $role = null): array
    {
        $role = $role ?? Auth::user()?->role ?? 'kreditor';
        $settings = static::getFieldSettings();

        return $settings
            ->filter(fn ($s) => $s->required && in_array($role, $s->roles ?? []))
            ->keys()
            ->toArray();
    }

    public function getDisplayNumberAttribute()
    {
        return $this->sagsnr ?? $this->id;
    }

    protected function normalize(?string $value): string
    {
        $value = mb_strtolower(trim((string) $value));
        $value = str_replace(['–', '—'], '-', $value);

        return trim(
            preg_replace('/\s+/', ' ', $value)
        );
    }

    public function scopeUnreadForUser($query, $user)
    {
        return $query->whereHas('users', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->whereNull('read_at');
        });
    }

    /* =========================================================================
     * BOOTED & GLOBAL SCOPES
     * ========================================================================= */

    protected static function booted()
    {
        parent::booted();

        static::forceDeleted(function ($sag) {
            $sag->sagerdebitor()->detach();
            $sag->sagerkreditor()->detach();
            $sag->sagersagsbehandler()->detach();
            $sag->sagerkonsulent()->detach();
            $sag->sagertokens()->detach();

            $sag->sagerStatus()->detach();
            $sag->sagerKtr()->detach();
            $sag->sagerBemaerkning()->detach();
            $sag->sagerAfslutning()->detach();
            $sag->sagerUdlaeg()->detach();
            
            $sag->dialogs()->delete();
            $sag->dokumenter()->delete();
            $sag->activities()->delete();
        });

        static::addGlobalScope('kreditor', function ($query) {
            if (! auth()->check()) {
                return;
            }

            $kreditorId = auth()->user()
                ->kreditorer()
                ->value('kreditors.id');

            if ($kreditorId) {
                $query->whereHas('sagerkreditor', function ($q) use ($kreditorId) {
                    $q->whereKey($kreditorId);
                });
            }
        });
    }

    /* =========================================================================
     * GDPR & RETENTION (OPDATERET OG OPTIMERET)
     * ========================================================================= */

    public function getRetentionDateAttribute()
    {
        return $this->afsluttet ?? $this->dato;
    }

    public function getGdprDeadlineAttribute(): ?Carbon
    {
        return $this->retention_date ? Carbon::parse($this->retention_date)->addYears(5) : null;
    }

    public function getGdprDaysLeftAttribute(): ?int
    {
        if (! $this->gdpr_deadline) {
            return null;
        }

        return (int) now()->diffInDays($this->gdpr_deadline, false);
    }

    public function getGdprStatusAttribute(): array
    {
        $days = $this->gdpr_days_left;

        if ($days === null) {
            return [
                'code' => 'active',
                'label' => 'Aktiv / Ikke afsluttet',
                'color' => 'slate',
                'days_left' => null,
            ];
        }

        if ($days <= 0) {
            return [
                'code' => 'expired',
                'label' => 'Klar til anonymisering (> 5 år)',
                'color' => 'red',
                'days_left' => $days,
            ];
        }

        if ($days <= 365) {
            return [
                'code' => 'warning',
                'label' => 'Udløber snart (4-5 år)',
                'color' => 'amber',
                'days_left' => $days,
            ];
        }

        return [
            'code' => 'ok',
            'label' => 'Overholdt',
            'color' => 'emerald',
            'days_left' => $days,
        ];
    }

    public function isEligibleForGdprDeletion(): bool
    {
        return $this->gdpr_days_left !== null && $this->gdpr_days_left <= 0;
    }

    public function anonymize(): void
    {
        // 1. Detach personhenførbare relationer
        $this->sagerdebitor()->detach();
        $this->sagerkreditor()->detach();
        $this->sagersagsbehandler()->detach();
        $this->sagerkonsulent()->detach();
        $this->sagertokens()->detach();

        // 2. Slet relaterede følsomme samtaler og dokumenter
        $this->dialogs()->delete();
        $this->dokumenter()->delete();
        $this->activities()->delete();

        // 3. Anonymiser selve sagens felter
        $this->sagsnr = 'ANONYMISERET-' . $this->id;
        $this->stelnr = null;
        $this->fakturanr = null;
        $this->kode = null;
        $this->kort_bemaerkning = 'Anonymiseret i henhold til GDPR 5-års reglen.';

        $this->save();
    }

    public function anonymizeRelations(): void
    {
        $this->anonymize();
    }

    /* Scopes til direkte database-forespørgsler */
    public function scopeGdprExpired($query)
    {
        return $query->whereNotNull('afsluttet')
            ->where('afsluttet', '<=', now()->subYears(5));
    }

    public function scopeGdprExpiringSoon($query)
    {
        return $query->whereNotNull('afsluttet')
            ->whereBetween('afsluttet', [
                now()->subYears(5),
                now()->subYears(4),
            ]);
    }

    // Bagudkompatible scopes
    public function scopeEligibleForRetention($query)
    {
        return $this->scopeGdprExpired($query);
    }

    public function scopeExpiringSoon($query)
    {
        return $this->scopeGdprExpiringSoon($query);
    }
}