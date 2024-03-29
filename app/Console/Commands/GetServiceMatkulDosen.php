<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetServiceMatkulDosen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getService:matkulDosen {fakultas_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarik service matakuliah dosen per-Fakultas';

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
        
        $matakuliah = new \App\Dosen;
        $result = $matakuliah->get_matakuliah_dosen($fakultas_id);

        $this->info($result);
    }
}
