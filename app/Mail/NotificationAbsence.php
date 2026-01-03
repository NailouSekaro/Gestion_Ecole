<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationAbsence extends Mailable
{
    use Queueable, SerializesModels;

    public $eleve;
    public $absencesCount;

    public function __construct($eleve, $absencesCount)
    {
        $this->eleve = $eleve;
        $this->absencesCount = $absencesCount;
    }

    public function build()
    {
        return $this->subject('Alerte Absences - ' . $this->eleve->nom . ' ' . $this->eleve->prenom)
                    ->view('emails.notification_absence');
    }
}
