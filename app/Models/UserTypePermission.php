<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class UserTypePermission extends Model
{
    use HasFactory;

    protected $table = 'user_type_permissions';

    protected $fillable = [
        'user_type_id',
        'permission_id',
    ];

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
