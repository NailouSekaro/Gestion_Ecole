<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class matiere extends Model
{
    use HasFactory;
    protected $guarded = [''];
    protected $fillable = ['nom'];

    public function coefficients()
    {
        return $this->hasMany(Coefficient::class);
    }

    public function classes()
    {
        // Récupérer les classes associées à une matière via la table coefficients
        return $this->belongsToMany(Classe::class, 'coefficients', 'matiere_id', 'classe_id');
    }

    public function enseignants()
    {
        return $this->hasMany(Enseignant::class);
    }
}
