<?php

namespace App\Console\Commands\Innpulsa;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test command report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info("Entry to command");
    }
}
