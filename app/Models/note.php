<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Mail\NotesEleveMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class note extends Model
{
    use HasFactory;
    protected $guarded = [''];
    protected $fillable = ['valeur_note', 'type_evaluation', 'eleve_id', 'matiere_id', 'trimestre_id', 'classe_id', 'annee_academique_id', 'envoye', 'modifie'];

    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function trimestre()
    {
        return $this->belongsTo(Trimestre::class);
    }

    // public function eleve()
    // {
    //     return $this->belongsTo(Eleve::class);
    // }

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }

    public function classe()
    {
        return $this->belongsTo(classe::class, 'classe_id');
    }

    public function annee_academique()
    {
        return $this->belongsTo(annee_academique::class, 'annee_academique_id');
    }

    // protected static function booted()
    // {
    //     static::created(function ($note) {
    //         $eleve = $note->eleve;

    //         if (!$eleve->email_parent) {
    //             return;
    //         }

    //         // Génération du PDF
    //         $pdf = Pdf::loadView('pdf.bulletin_notes', compact('eleve'));
    //         $pdfPath = storage_path('app/public/Bulletin_' . $eleve->nom . '.pdf');
    //         $pdf->save($pdfPath);

    //         // Envoi de l'email
    //         Mail::to($eleve->email_parent)->send(new NotesEleveMail($eleve, $pdfPath));
    //     });
    // }

    protected static function booted()
    {
        static::created(function ($note) {
            $eleve = $note->eleve;

            if (!$eleve->email_parent) {
                return;
            }

            // Récupérer uniquement les notes non envoyées
            $notesNonEnvoyees = $eleve->notes()->where('envoye', false)->get();

            if ($notesNonEnvoyees->isEmpty()) {
                return;
            }

            // Génération du PDF avec uniquement les nouvelles notes
            $pdf = Pdf::loadView('pdf.bulletin_notes', [
                'eleve' => $eleve,
                'notes' => $notesNonEnvoyees,
            ]);

            $pdfPath = storage_path('app/public/Bulletin_' . $eleve->nom . '.pdf');
            $pdf->save($pdfPath);

            // Envoi de l'email
            Mail::to($eleve->email_parent)->send(new NotesEleveMail($eleve, $pdfPath));

            // Marquer les notes comme envoyées
            $eleve
                ->notes()
                ->where('envoye', false)
                ->update(['envoye' => true]);
        });

        static::updated(function ($note) {
            if ($note->wasChanged('valeur_note') && $note->envoye) {
                // Marquer la note comme modifiée
                $note->update(['modifie' => true]);

                // Récupérer l'élève
                $eleve = $note->eleve;

                // Générer un nouveau PDF du bulletin
                $pdf = Pdf::loadView('pdf.bulletin_notes', compact('eleve'));
                $pdfPath = storage_path('app/public/Bulletin_' . $eleve->nom . '.pdf');
                $pdf->save($pdfPath);

                // Envoyer un email aux parents avec le nouveau bulletin
                Mail::to($eleve->email_parent)->send(new NotesModifieesMail($eleve, $pdfPath));

                // Réinitialiser le statut après envoi
                $note->update(['modifie' => false]);
            }
        });
    }
}
