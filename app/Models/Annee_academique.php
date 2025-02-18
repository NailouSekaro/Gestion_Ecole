<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annee_academique extends Model
{
    use HasFactory;

    protected $guarded = [''];
    protected $fillable = ['annee', 'date_debut', 'date_fin'];

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    protected $table = 'annee_academiques';

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    // Relation avec les enseignants via la table pivot
    // public function enseignants()
    // {
    //     return $this->belongsToMany(Enseignant::class, 'enseignant_classe')
    //         ->withPivot('classe_id')
    //         ->withTimestamps();
    // }

    public function enseignants()
    {
        return $this->belongsToMany(Enseignant::class, 'enseignant_classe', 'annee_academique_id', 'enseignant_id');;
    }
}
