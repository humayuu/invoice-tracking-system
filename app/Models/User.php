<?php

namespace App\Models;

use App\Notifications\CustomVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'google_avatar',
        'created_with_google',
        'profile_photo_path',
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
            'created_with_google' => 'boolean',
        ];
    }

    /**
     * Send custom email verification
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Whether the user can sign in with a password they chose (not Google-only signup).
     */
    public function hasPassword(): bool
    {
        return ! $this->created_with_google;
    }

    /**
     * Check if user registered via Google
     */
    public function isGoogleUser(): bool
    {
        return ! is_null($this->google_id);
    }

    /**
     * Get profile photo URL (safe fallback)
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->created_with_google && $this->google_avatar) {
            return $this->google_avatar;
        }

        if ($this->profile_photo_path) {
            return Storage::disk('public')
                ->url($this->profile_photo_path);
        }

        if ($this->google_avatar) {
            return $this->google_avatar;
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name)
            .'&background=6366f1&color=fff&size=120';
    }
}
