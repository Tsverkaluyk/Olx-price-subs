<?php

namespace App\Console\Commands;

use App\Jobs\CheckOlxPricesJob;
use Illuminate\Console\Command;

class CheckOlxPrices extends Command
{
    protected $signature = 'olx:check-prices';
    protected $description = 'Check OLX ads prices and notify subscribers about changes';

    public function handle()
    {
        CheckOlxPricesJob::dispatch();
    }
}
