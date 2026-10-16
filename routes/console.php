<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('chargegrid:status', function () {
    $this->info('ChargeGrid base project initialized.');
})->purpose('Show the ChargeGrid project initialization status');
