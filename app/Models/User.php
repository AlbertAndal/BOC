<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, HasRoles, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'username',
        'password',
        'role_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user can access the Filament panel.
     * Administrator approval is required (status must be 'Active').
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->status !== 'Active') {
            return false;
        }

        return match ($panel->getId()) {
            'auth' => $this->isAdmin() || $this->isAccountsManager() || $this->isCustomsBroker(),
            'admin' => $this->isAdmin(),
            'manager' => $this->isAccountsManager(),
            'broker' => $this->isCustomsBroker(),
            default => false,
        };
    }

    /**
     * Display name in Filament UI.
     */
    public function getFilamentName(): string
    {
        return $this->full_name ?: $this->username ?: $this->email;
    }

    public static function current(): ?self
    {
        $user = Auth::user();

        return $user instanceof self ? $user : null;
    }

    /**
     * Role checking helpers
     */
    public function isAdmin(): bool
    {
        return $this->role?->role_name === 'Administrator' || $this->hasRole('Administrator');
    }

    public function isAccountsManager(): bool
    {
        return $this->role?->role_name === 'Accounts Manager' || $this->hasRole('Accounts Manager');
    }

    public function isCustomsBroker(): bool
    {
        return $this->role?->role_name === 'Customs Broker' || $this->hasRole('Customs Broker');
    }

    public function hasRoleName(string $name): bool
    {
        return $this->role?->role_name === $name || $this->hasRole($name);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function assignedIssues(): HasMany
    {
        return $this->hasMany(Issue::class, 'assigned_manager', 'user_id');
    }

    public function createdIssues(): HasMany
    {
        return $this->hasMany(Issue::class, 'created_by', 'user_id');
    }

    public function issueActions(): HasMany
    {
        return $this->hasMany(IssueAction::class, 'performed_by', 'user_id');
    }
}
