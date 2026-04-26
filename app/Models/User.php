<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** @var list<string> */
    public const MODULE_KEYS = [
        'dashboard',
        'sales',
        'purchase',
        'clients',
        'suppliers',
        'reports',
    ];

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'is_admin',
        'permissions',
        'is_active',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessModule(string $module): bool
    {
        if ($this->is_admin) {
            return true;
        }

        $permissions = $this->permissions;

        return is_array($permissions) && in_array($module, $permissions, true);
    }

    /**
     * First module URL the user may open after login (profile is always allowed separately).
     */
    public function firstAccessibleUrl(): string
    {
        $map = [
            'dashboard' => fn () => route('dashboard'),
            'sales' => fn () => route('sales.index'),
            'purchase' => fn () => route('purchase.index'),
            'clients' => fn () => route('client.index'),
            'suppliers' => fn () => route('supplier.index'),
            'reports' => fn () => route('reports.index'),
        ];

        foreach ($map as $module => $resolver) {
            if ($this->canAccessModule($module)) {
                return $resolver();
            }
        }

        return route('profile');
    }

    /**
     * Get profile photo URL (safe fallback)
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return Storage::disk('public')
                ->url($this->profile_photo_path);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name)
            .'&background=6366f1&color=fff&size=120';
    }
}
