<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breed extends Model
{
    protected $table = 'breeds';
    protected $fillable = ['species_id', 'name'];

    public function especie()
    {
        return $this->belongsTo(Species::class);
    }
}
