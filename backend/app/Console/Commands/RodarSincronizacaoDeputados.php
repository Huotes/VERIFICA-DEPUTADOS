<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SincronizarDeputados;

class RodarSincronizacaoDeputados extends Command
{
    protected $signature = 'sincronizar:deputados';
    protected $description = 'Sincroniza deputados e delega despesas para cada um em fila';

    public function handle(): int
    {
        SincronizarDeputados::dispatch();
        $this->info("Job de sincronização de deputados despachado.");
        return 0;
    }
}
