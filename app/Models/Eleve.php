<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    use HasFactory;

    protected $guarded = [''];
    protected $fillable = ['matricule_educ_master', 'nom', 'prenom', 'date_naissance', 'lieu_de_naissance', 'sexe', 'email_parent', 'contact_parent', 'aptitude_sport', 'photo', 'nationalite'];

    public function classe()
    {
        return $this->belongsTo(classe::class);
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function inscription()
    {
        return $this->hasOne(Inscription::class)->latestOfMany();
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    // Modèle Note
    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'inscriptions', 'eleve_id', 'classe_id');
    }

    // public function getPhotoUrlAttribute()
    // {
    //     if ($this->photo) {
    //         return asset('storage/' . $this->photo);
    //     }

    //     return $this->photo ? asset('storage/' . $this->photo) : asset('public/images/default-avatar.jpg');
    // }
}
