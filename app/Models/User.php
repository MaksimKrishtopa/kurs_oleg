<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'surname',
        'name',
        'patronymic',
        'gender',
        'date_of_birth',
        'phone',
        'email',
        'password',
        'role_id', // Добавлено поле role_id
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at'
    ];

    // Добавляем роль пользователя
    public function isAdmin() {
        return $this->role_id === 1;
    }
}