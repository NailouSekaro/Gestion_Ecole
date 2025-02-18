<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Models\enseignant_classe;

class enseignant extends Model {
    use HasFactory;
    protected $guarded = [ '' ];
    protected $fillable = [ 'user_id', 'matricule', 'telephone', 'sexe', 'diplomes', 'adresse', 'matiere_id' ];

    // Relation avec User

    public function user() {
        return $this->belongsTo( User::class );
    }

    // Relation avec les classes via la table pivot

    public function classes() {
        return $this->belongsToMany( Classe::class, 'enseignant_classe' )
        ->withPivot( 'annee_academique_id' )
        ->withTimestamps();
    }

    // Relation avec les années académiques

    // public function anneesAcademiques() {
    //     return $this->belongsToMany( annee_academique::class, 'enseignant_classe' )
    //     ->withPivot( 'classe_id' )
    //     ->withTimestamps();
    // }

    public function anneeAcademique() {
        return $this->belongsToMany( annee_academique::class, 'enseignant_classe', 'enseignant_id', 'annee_academique_id' );
    }

    public function matiere() {
        return $this->belongsTo( Matiere::class, 'matiere_id' );
    }

}
