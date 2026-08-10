<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use SoftDeletes; // 🟢 2. Tag SoftDeletes i brug
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected ?string $cachedRole = null;

    public function kreditorer(): BelongsToMany
    {
        return $this->belongsToMany(
            Kreditorer::class,
            'kreditor_user',
            'user_id',
            'kreditor_id'
        );
    }

    public function sagActivities(): HasMany
    {
        return $this->hasMany(SagActivity::class);
    }
    
    // in User model
    public function scopeRole($query, $roles)
    {
        return $query->whereHas('roles', fn($q) => $q->whereIn('name', (array) $roles));
    }

    public function dashboardRoute(): string
    {
        return match ($this->role()) {
            'Admin' => 'admin.dashboard',
            'Medarbejder' => 'medarbejder.dashboard',
            'Kreditor' => 'kreditor.dashboard',
            default => 'dashboard',
        };
    }

    public function requiresTwoFactor(): bool
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains(
                fn ($role) => (bool) $role->requires_two_factor
            );
        }

        return $this->roles()
            ->where('requires_two_factor', true)
            ->exists();
    }

    public function dashboardUrl(): string
    {
        return route($this->dashboardRoute());
    }

    public function isDashboardRoute(): bool
    {
        return request()->routeIs($this->dashboardRoute());
    }

    public function isAdmin(): bool
    {
        return $this->role() === 'Admin';
    }

    public function isMedarbejder(): bool
    {
        return $this->role() === 'Medarbejder';
    }

    public function isKreditor(): bool
    {
        return $this->role() === 'Kreditor';
    }

    public function hasConfiguredTwoFactor(): bool
    {
        return ! empty($this->two_factor_secret)
            && ! is_null($this->two_factor_confirmed_at);
    }

    public function role(): ?string
    {
        if ($this->cachedRole !== null) {
            return $this->cachedRole;
        }

        if ($this->relationLoaded('roles')) {
            return $this->cachedRole = $this->roles->first()?->name;
        }

        return $this->cachedRole = $this->roles()
            ->value('name');
    }
}