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
        'username',
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
        return Menu::whereNull('parent_id')
                    ->where(function (Builder $query) {
                        $permissions = $this->permissions->pluck('id')->push(...$this->roles->pluck('permissions')->flatten()->pluck('id'));

                        $query->whereHas('permissions', function (Builder $query) use ($permissions) {
                            $query->whereIn('permissions.id', $permissions);
                        })->orDoesntHave('permissions');
                    })
                    ->orderBy('position')
                    ->with('childsByPermissions')
                    ->get()
                    ->each([$this, 'changeChildsByPermissionsToChilds']);
    }

    /**
     * @param \App\Models\Menu $menu
     * @return void
     */
    public function changeChildsByPermissionsToChilds(Menu $menu)
    {
        $menu->childs = collect($menu->childsByPermissions)->each([$this, 'changeChildsByPermissionsToChilds']);
    }
}
