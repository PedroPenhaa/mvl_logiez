<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Atualizar status de pagamentos a cada 10 minutos
        $schedule->command('app:atualizar-status-pagamentos')
                ->everyTenMinutes()
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/atualizar-pagamentos.log'));
        
        // Processar envios pendentes a cada 10 minutos, após atualizar os pagamentos
        $schedule->command('app:processar-envios-pendentes')
                ->everyTenMinutes()
                ->withoutOverlapping()
                ->after(function () {
                    Log::info('Comando processar-envios-pendentes executado com sucesso');
                })
                ->appendOutputTo(storage_path('logs/processar-envios.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 