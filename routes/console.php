<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
*/


//Tareas programadas
Schedule::command('app:procesar-seguimiento-vigilancia-epi')->everyThreeMinutes();
//Schedule::command('app:procesar-seguimiento-vigilancia-epi')->everyMinute();

Schedule::command('app:notificar-seguimiento-vigilancia-epi')->everyMinute();
