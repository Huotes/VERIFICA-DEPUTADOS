<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Deputado;
use Illuminate\Support\Facades\Http;

class ImportaDeputados extends Command
{
    protected $signature = 'importa:deputados';
    protected $description = 'Importa deputados';
}
