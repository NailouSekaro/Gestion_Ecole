<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Models\EnseignantClasse;

class classe extends Model
{
    use HasFactory;

    protected $guarded = [''];
    protected $fillable = ['nom_classe', 'cycle', 'frais_scolarite'];

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function coefficients()
    {
        return $this->hasMany(Coefficient::class);
    }

    public function matieres()
    {
        // Récupérer les matières associées à une classe via la table coefficients
        return $this->belongsToMany(Matiere::class, 'coefficients', 'classe_id', 'matiere_id');
    }

    // Relation avec les enseignants via la table pivot
    public function enseignants()
    {
        return $this->belongsToMany(Enseignant::class, 'enseignant_classe')
            ->withPivot('annee_academique_id')
            ->withTimestamps();
    }
}
