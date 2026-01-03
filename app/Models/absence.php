<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $fillable = ['eleve_id', 'annee_academique_id', 'trimestre_id', 'matiere_id', 'classe_id', 'date_absence', 'type', 'justification', 'justifiee', 'user_id'];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function anneeAcademique()
    {
        return $this->belongsTo(Annee_academique::class);
    }

    public function trimestre()
    {
        return $this->belongsTo(Trimestre::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


