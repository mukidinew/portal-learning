<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetServiceProdi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getService:prodi {fakultas_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarik service prodi per-Fakultas setiap periode';

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

        $prodi = new \App\Prodi;
        $result = $prodi->get_prodi($fakultas_id);

        $this->info($result);
    }
}
