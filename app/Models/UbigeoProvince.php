<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Provincia de Perú (PK tipo CHAR 4)
class UbigeoProvince extends Model
{
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $table = 'ubigeo_provinces';

    protected $fillable = ['id', 'name', 'department_id'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(UbigeoDepartment::class, 'department_id');
    }

    public function districts(): HasMany
    {
        return $this->hasMany(UbigeoDistrict::class, 'province_id');
    }
}
