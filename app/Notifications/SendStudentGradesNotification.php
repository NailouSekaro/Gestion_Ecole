<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Matiere;

class SendStudentGradesNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $eleve;
    public $notes;

    public function __construct($eleve, $notes)
    {
        $this->eleve = $eleve;
        $this->notes = $notes;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Bulletin de notes hebdomadaire de ' . $this->eleve->nom)
            ->greeting('Bonjour cher parent,')
            ->line('Voici les notes de votre enfant ' . $this->eleve->nom . ' cette semaine :');

        foreach ($this->notes as $note) {
            $matiere = Matiere::find($note->matiere_id)->nom ?? 'Inconnue';
            $type = ucfirst($note->type_evaluation);
            $mail->line("$matiere ($type) : **{$note->valeur_note}/20**");
        }

        return $mail->line('Merci de votre confiance !');
    }
}

