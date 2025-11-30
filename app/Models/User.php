<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Surat yang dibuat user (kolom created_by)
    public function suratDibuat()
    {
        return $this->hasMany(Surat::class, 'created_by');
    }

    // Surat yang terakhir diupdate user (kolom updated_by)
    public function suratDiupdate()
    {
        return $this->hasMany(Surat::class, 'updated_by');
    }

    // Surat yang dapat diakses user (via pivot surat_user)
    public function suratDiterima()
    {
        return $this->belongsToMany(Surat::class, 'surat_user', 'user_id', 'surat_id')
            ->withTimestamps();
    }

    // (opsional) alias singkat, kalau mau:
    public function surat()
    {
        return $this->suratDiterima();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
