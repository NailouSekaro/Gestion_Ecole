<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conduite extends Model
{
    protected $fillable = ['eleve_id', 'annee_academique_id', 'trimestre_id', 'classe_id', 'valeur_note'];

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
}
