<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\Roles;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

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
            'is_active' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(Roles::SUPER_ADMIN);
    }

    /**
     * Libellé français du rôle principal affiché dans le back-office.
     */
    public function roleLabel(): string
    {
        return match (true) {
            $this->hasRole(Roles::SUPER_ADMIN) => 'Super Admin',
            $this->hasRole(Roles::MANAGER) => 'Gestionnaire',
            $this->hasRole(Roles::CASHIER) => 'Caissière',
            default => 'Compte',
        };
    }
}
