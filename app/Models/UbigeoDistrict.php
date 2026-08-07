<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Distrito de Perú (PK tipo CHAR 6)
class UbigeoDistrict extends Model
{
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $table = 'ubigeo_districts';

    protected $fillable = ['id', 'name', 'province_id', 'department_id'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(UbigeoProvince::class, 'province_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(UbigeoDepartment::class, 'department_id');
    }
}
