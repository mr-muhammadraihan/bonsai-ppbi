<?php

namespace App\Models;

use Database\Factories\BonsaiTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BonsaiType extends Model
{
    /** @use HasFactory<BonsaiTypeFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    public function bonsais(): HasMany
    {
        return $this->hasMany(Bonsai::class);
    }
}
