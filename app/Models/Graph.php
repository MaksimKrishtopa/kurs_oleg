<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Graph extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'date_and_time',
        'doctor_id'
    ];

    protected $hidden = [
      'created_at',
      'updated_at',
      'deleted_at'
    ];
}
