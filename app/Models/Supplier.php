<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'name',
        'ruc',
        'phone',
        'email',
        'address',
        'country',
        'state',
        'city',
        'contact_name',
        'is_active'
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
