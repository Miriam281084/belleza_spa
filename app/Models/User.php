<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
=======
>>>>>>> f24e2ba2bb9ad6aefdb86e2dad1670bca014d857
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }
<<<<<<< HEAD

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('nombre', $role)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('nombre', $roles)->exists();
    }
=======
>>>>>>> f24e2ba2bb9ad6aefdb86e2dad1670bca014d857
}
