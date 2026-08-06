<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Konsulenter extends Model
{
    use LogsActivity;
    
    protected $table = 'konsulenters';
    
    protected $fillable = [
        'navn',
        'email',
        'tlf',
        'mobil',
    ];

    /*
    |--------------------------------------------------------------------------
    | Cases
    |--------------------------------------------------------------------------
    */

    public function sager(): BelongsToMany
    {
        return $this->belongsToMany(
            Sager::class,
            'sager_konsulent',
            'konsulent_id',
            'sag_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    public function hovedRole(): HasOne
    {
        return $this->hasOne(
            HovedKonsulent::class,
            'hoved_konsulent_id',
            'id'
        );
    }


    public function skjultRole(): HasMany
    {
        return $this->hasMany(
            SkjultKonsulent::class,
            'skjult_konsulent_id',
            'id'
        );
    }


    public function notifikationRole(): HasMany
    {
        return $this->hasMany(
            NotifikationsKonsulent::class,
            'notifikations_konsulent_id',
            'id'
        );
    }



    /*
    |--------------------------------------------------------------------------
    | Convenience helpers
    |--------------------------------------------------------------------------
    */


    public function isHoved(): bool
    {
        return HovedKonsulent::current()?->id === $this->id;
    }


    public function isSkjult(): bool
    {
        return (bool) $this->skjult_role_exists;
    }

    public function isNotifikation(): bool
    {
        return (bool) $this->notifikation_role_exists;
    }


    public function receivesNotifications(): bool
    {
        return $this->notifikationRole()->exists();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()

            ->logOnly([
                'navn',
                'email',
                'tlf',
                'mobil',
            ])

            ->logOnlyDirty()

            ->dontSubmitEmptyLogs();
    }

    public function visibleRoles(): array
    {
        if ($this->isHoved()) {
            return [
                [
                    'name' => 'Hovedkonsulent',
                    'icon' => '⭐',
                    'class' => 'bg-indigo-100 text-indigo-700',
                ],
            ];
        }


        $roles = [];


        if ($this->isSkjult()) {
            $roles[] = [
                'name' => 'Skjult',
                'icon' => '🙈',
                'class' => 'bg-slate-200 text-slate-700',
            ];
        }


        if ($this->isNotifikation()) {
            $roles[] = [
                'name' => 'Notifikation',
                'icon' => '🔔',
                'class' => 'bg-emerald-100 text-emerald-700',
            ];
        }


        if (empty($roles)) {
            $roles[] = [
                'name' => 'Standard',
                'icon' => '',
                'class' => 'bg-slate-100 text-slate-700',
            ];
        }


        return $roles;
    }
}