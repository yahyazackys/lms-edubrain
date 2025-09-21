<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method bool hasAnyRole(...$roles)
 * @method bool hasRole(string|array|\Spatie\Permission\Contracts\Role $roles)
 * @method bool hasPermissionTo(string|int|\Spatie\Permission\Contracts\Permission $permission, $guardName = null)
 */

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasRoles;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'username',
        'nama',
        'password',
        'email',
        'no_hp',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }


    // Relasi ke Mahasiswa (jika role = mahasiswa)
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'id_pengguna', 'id_pengguna');
    }

    // Relasi ke Dosen (jika role = dosen)
    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'id_pengguna', 'id_pengguna');
    }
}
