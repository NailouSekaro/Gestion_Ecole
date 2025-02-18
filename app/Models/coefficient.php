<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class coefficient extends Model {
    use HasFactory;
    protected $guarded = [ '' ];
    protected $fillable = [ 'classe_id', 'matiere_id', 'valeur_coefficient' ];

    public function classe() {
        return $this->belongsTo( Classe::class );
    }

    public function matiere() {
        return $this->belongsTo( Matiere::class );
    }

}
