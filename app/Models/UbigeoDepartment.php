<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Departamento de Perú (PK tipo CHAR 2)
class UbigeoDepartment extends Model
{
    use HasFactory;

    protected $table = 'ubigeo_departments';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = ['id', 'name'];

    public function provinces(): HasMany
    {
        return $this->hasMany(UbigeoProvince::class, 'department_id');
    }
}
