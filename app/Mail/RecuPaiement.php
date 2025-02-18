<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Paiement;

class RecuPaiement extends Mailable
{
    use Queueable, SerializesModels;

    public $paiement;
    public $pdfPath;

    public function __construct(Paiement $paiement, $pdfPath)
    {
        $this->paiement = $paiement;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->subject('Reçu de paiement - École')
            ->view('emails.recu_paiement')
            ->attach($this->pdfPath, [
                'as' => 'Recu_Paiement.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
