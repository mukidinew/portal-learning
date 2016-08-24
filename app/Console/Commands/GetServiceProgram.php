<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetServiceProgram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getService:program {fakultas_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarik service program per-Fakultas setiap periode';

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
        $fakultas_id = $this->argument('fakultas_id');
        
        $program = new \App\Program;
        $result = $program->get_program($fakultas_id);

        $this->info($result);
    }
}
