<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Tâches planifiées
|--------------------------------------------------------------------------
*/

// Purge RGPD des pièces d'identité : chaque nuit à 3h.
Schedule::command('reservations:purge-identity-documents')->dailyAt('03:00');
