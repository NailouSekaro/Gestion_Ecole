<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploiTemps extends Model
{
    protected $table = 'emplois_temps';
    protected $fillable = ['classe_id', 'matiere_id', 'annee_academique_id', 'jour', 'heure_debut', 'heure_fin'];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class);
    }

    public function annee_academique()
    {
        return $this->belongsTo(annee_academique::class);
    }
}
