<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'surname',
        'name',
        'patronymic',
        'gender',
        'date_of_birth',
        'specialization_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
