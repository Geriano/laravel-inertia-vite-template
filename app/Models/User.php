<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasPermissions;
    use HasRoles;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * @return \Illuminate\Support\Collection
     */
    public function menus()
    {
        return Menu::where(function (Builder $query) {
                        $this->roles->each(function (Role $role) use ($query) {
                            $query->orWhereHas('permissions', function (Builder $query) use ($role) {
                                $query->whereIn('permissions.id', $role->permissions->pluck('id')->toArray());
                            });
                        });

                        $query->orWhereHas('roles', function (Builder $query) {
                            $query->whereIn('roles.id', $this->roles->pluck('id')->toArray());
                        });

                        $query->orWhereHas('permissions', function (Builder $query) {
                            $query->whereIn('permissions.id', $this->permissions->pluck('id')->toArray());
                        });
                    })
                    ->orWhere(function (Builder $query) {
                        $query->orDoesntHave('permissions');
                        $query->orDoesntHave('roles');
                    })
                    ->get();
    }
}
