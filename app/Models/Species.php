<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Species extends Model
{
    protected $table = 'species';
    protected $fillable = ['name'];

    public function razas()
    {
        return $this->hasMany(Breed::class);
    }
}
