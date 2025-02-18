<?php

class PaiementCorrigeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $paiement;
    public $pdfPath;

    public function __construct($paiement, $pdfPath)
    {
        $this->paiement = $paiement;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->subject('Correction de paiement pour votre enfant')->markdown('emails.paiement_corrige')->attach($this->pdfPath);
    }
}
