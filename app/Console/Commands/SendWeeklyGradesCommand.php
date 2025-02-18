<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Eleve;
use App\Models\Note;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendStudentGradesNotification;
use Carbon\Carbon;

class SendWeeklyGradesCommand extends Command
{
    protected $signature = 'send:weekly-grades';
    protected $description = 'Envoie les notes aux parents une fois par semaine';

    public function handle()
    {
        $oneWeekAgo = Carbon::now()->subWeek(); // Récupère les notes des 7 derniers jours

        $eleves = Eleve::with(['notes' => function ($query) use ($oneWeekAgo) {
            $query->where('created_at', '>=', $oneWeekAgo);
        }])->get();

        foreach ($eleves as $eleve) {
            if ($eleve->notes->isNotEmpty() && $eleve->email_parent) {
                Notification::route('mail', $eleve->email_parent)
                    ->notify(new SendStudentGradesNotification($eleve, $eleve->notes));
            }
        }

        $this->info('Les notes ont été envoyés avec succès.');
    }
}
