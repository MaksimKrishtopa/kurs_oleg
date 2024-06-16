<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialization_id',
        'doctor_id',
        'graph_id',
        'user_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
