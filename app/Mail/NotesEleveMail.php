<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotesEleveMail extends Mailable {
    use Queueable, SerializesModels;

    public $eleve;
    public $pdfPath;

    public function __construct( $eleve, $pdfPath ) {
        $this->eleve = $eleve;
        $this->pdfPath = $pdfPath;
    }

    public function build() {
        return $this->view( 'emails.notes_eleve' )
        ->subject( 'Bulletin de notes de votre enfant' )
        ->attach( $this->pdfPath, [
            'as' => 'Bulletin_' . $this->eleve->nom . '.pdf',
            'mime' => 'application/pdf',
        ] );
    }

    // public function build()
    // {
    //     return $this->subject( 'Bulletin de notes de ' . $this->eleve->nom )
    //                 ->view( 'emails.notes_eleve' )
    //                 ->attach( $this->pdfPath, [
    //                     'as' => 'Bulletin_' . $this->eleve->nom . '.pdf',
    //                     'mime' => 'application/pdf',
    // ] );
    // }
}
