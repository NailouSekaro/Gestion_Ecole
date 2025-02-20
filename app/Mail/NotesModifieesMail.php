<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotesModifieesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $eleve;
    public $pdfPath;

    public function __construct($eleve, $pdfPath)
    {
        $this->eleve = $eleve;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->subject('Mise à jour des notes de votre enfant')->markdown('emails.notes_modifiees')->attach($this->pdfPath);
    }
}
