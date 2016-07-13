<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetServiceMatkulMahasiswa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getService:matkulMahasiswa {fakultas_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $periode_id = \App\Periode::orderBy('id', 'DESC')->first()->id;
        $fakultas_id = $this->argument('fakultas_id');
        
        $matakuliah = new \App\MatkulMahasiswa;
        $result = $matakuliah->get_matkul($periode_id, $fakultas_id);

        $this->info($result);
    }
}
