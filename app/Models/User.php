<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'job_title',
        'email',
        'avatar_path',
        'password',
        'is_admin',
        'mis_role',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /** Root-relative `/storage/...` for same-site HTML. */
    public function avatarPublicUrl(): ?string
    {
        if ($this->avatar_path === null || $this->avatar_path === '') {
            return null;
        }

        return '/storage/'.$this->avatar_path;
    }

    public function profilePhotoUrl(): string
    {
        return $this->avatarPublicUrl()
            ?? 'https://www.gravatar.com/avatar/'.md5(strtolower(trim((string) $this->email))).'?d=identicon&s=64&r=pg';
    }

    public function deleteStoredAvatar(): void
    {
        if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
            Storage::disk('public')->delete($this->avatar_path);
        }
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user): void {
            $user->deleteStoredAvatar();
        });
    }

    public function canAccessMis(): bool
    {
        if ($this->is_admin) {
            return true;
        }

        return in_array($this->mis_role, ['finance', 'va'], true);
    }

    public function isMisVaOnly(): bool
    {
        return ! $this->is_admin && $this->mis_role === 'va';
    }

    public function isMisFinanceOnly(): bool
    {
        return ! $this->is_admin && $this->mis_role === 'finance';
    }
}
