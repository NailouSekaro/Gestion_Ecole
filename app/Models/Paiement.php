<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;
    protected $guarded = [''];
    protected $fillable = ['inscription_id', 'annnee_academique_id', 'montant', 'date_paiement', 'moyen_paiement', 'transaction_id', 'ancien_montant', 'corrige'];

    // 🔹 Relation avec Inscription
    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    // 🔹 Relation avec Année Académique
    public function anneeAcademique()
    {
        return $this->belongsTo(Annee_academique::class, 'annee_academique_id');
    }

    // 🔹 Cast de la date pour éviter l'erreur
    protected $casts = [
        'date_paiement' => 'datetime',
    ];
}
