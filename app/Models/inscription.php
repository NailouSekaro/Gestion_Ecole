<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inscription extends Model
{
    use HasFactory;
    protected $guarded = [''];
    protected $fillable = ['eleve_id', 'statut', 'type', 'classe_id', 'annee_academique_id'];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function Annee_academique()
    {
        return $this->belongsTo(Annee_academique::class);
    }
}
