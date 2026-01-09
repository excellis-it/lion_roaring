<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    //  protected $guard_name = 'api';
    protected $guard_name = 'web';

    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'created_id',
        'first_name',
        'last_name',
        'created_id',
        'user_name',
        'middle_name',
        'address',
        'email',
        'phone',
        'phone_country_code_name',
        'password',
        'country',
        'state',
        'ecclesia_id',
        'city',
        'state',
        'address2',
        'country',
        'zip',
        'time_zone',
        'fcm_token',
        'location_lat',
        'location_lng',
        'location_address',
        'location_zip',
        'location_country',
        'location_state',
        'profile_picture',
    ];
    protected $appends = ['ecclesia_access', 'full_name']; // Add this line

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }

    public function userSubscription()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function userLastSubscription()
    {
        return $this->hasOne(UserSubscription::class)->latest();
    }

    // chat relation
    public function chatSender()
    {
        return $this->hasMany(Chat::class, 'sender_id');
    }

    public function chatReciver()
    {
        return $this->hasMany(Chat::class, 'reciver_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_id');
    }

    public function ecclesia()
    {
        return $this->belongsTo(Ecclesia::class, 'ecclesia_id');
    }

    public function countries()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function states()
    {
        return $this->belongsTo(State::class, 'state');
    }

    // public function role()
    // {
    //     return $this->belongsToMany(Role::class);
    // }

    public function getFirstRoleType()
    {
        return $this->roles->pluck('type')->first();
    }

    public function systemNotifications()
    {
        return $this->hasMany(SystemNotification::class, 'notifiable_id');
    }

    public function getEcclesiaAccessAttribute()
    {
        if (!$this->manage_ecclesia) {
            return collect(); // Return an empty collection if null
        }

        $ecclesiaIds = explode(',', $this->manage_ecclesia); // Convert to an array

        return Ecclesia::whereIn('id', $ecclesiaIds)->get();
    }

    // is ecclesia user
    public function isEcclesiaUser()
    {
        return $this->roles->pluck('is_ecclesia')->first() == 1 ? true : false;
    }

    // Relationship for warehouses this user can manage
    public function warehouses()
    {
        return $this->belongsToMany(WareHouse::class, 'user_warehouses', 'user_id', 'warehouse_id')
            ->withTimestamps();
    }

    // Check if user is a warehouse admin
    public function isWarehouseAdmin()
    {
        // return $this->hasNewRole('WAREHOUSE_ADMIN');
        // check if user has any warehouses assigned
        return $this->warehouses()->exists();
    }

    // Check if user can manage a specific warehouse
    public function canManageWarehouse($warehouseId)
    {
        if ($this->hasNewRole('SUPER ADMIN')) {
            return true;
        }

        if ($this->isWarehouseAdmin()) {
            return $this->warehouses()->where('ware_houses.id', $warehouseId)->exists();
        }

        return false;
    }

    public function userRole()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }

    public function getFirstUserRoleType()
    {
        return $this->userRole->pluck('type')->first() ?? null;
    }

    public function getFirstUserRoleName()
    {
        return $this->userRole->pluck('name')->first() ?? null;
    }

    public function hasNewRole($roles)
    {
        if (is_array($roles)) {
            return $this->userRole()->whereIn('name', $roles)->exists();
        }

        return $this->userRole()->where('name', $roles)->exists();
    }
    // default delivery address
    public function defaultDeliveryAddress()
    {
        return $this->hasOne(UserAddress::class)->where('is_default', true);
    }
}
