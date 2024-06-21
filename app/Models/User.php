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

}
