<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentReason extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function citas()
    {
        return $this->hasMany(Appointment::class);
    }
}
