<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'no_hp',
    ];

    public function bonsais()
    {
        return $this->hasMany(Bonsai::class);
    }
}
