<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class trimestre extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'date_debut', 'date_fin', 'annee_academique_id'];

    public function Annee_academique()
    {
        return $this->belongsTo(Annee_academique::class);
    }

    public static function boot()
    {
        parent::boot();

        // Vérifier que les dates des trimestres sont valides
        static::saving(function ($trimestre) {
            $annee = $trimestre->Annee_academique;

            if ($annee) {
                if ($trimestre->date_debut < $annee->date_debut || $trimestre->date_fin > $annee->date_fin) {
                    throw new \App\Exceptions\InvalidTrimestreDatesException('Les dates du trimestre doivent être comprises dans la plage de l\'année académique.');
                }
            }
        });
    }

    protected $table = 'trimestres';

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];
}
