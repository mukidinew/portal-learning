<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Matakuliah extends Model
{
    protected $table = 'matakuliah';

    public function get_matakuliah($fakultas_id)
    {
    	$last_periode = \App\Periode::max('id');

    	$Program = DB::table('program')->where('fakultas_id', $fakultas_id)
    																->where('periode_id', $last_periode)
    																->get();

    	foreach($Program as $program)
    	{
    		$ch = curl_init();
	    	curl_setopt($ch, CURLOPT_URL, 'http://203.24.50.30:9099/datasnap/rest/tservermethods1/mk_jadwal/'.$last_periode.'/'.$program->prodi_id.'/'.$program->program_id);
	    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    	$service = curl_exec($ch);

	    	if($service === false)
	    	{
	    		return "Service Prodi Sedang Gangguan";
	    	}
	    	else
	    	{
	    		$data = json_decode($service, TRUE);
	    		foreach($data['result'][0] as $key => $value)
	    		{
	    			$hitung_matkul = $this->where('periode_id', $last_periode)
	    														->where('fakultas_id', $fakultas_id)
	    														->where('jadwal_id', $value['idjdw'])
	    														->count();
	    			if($hitung_matkul == 0)
	    			{
	    				$this->insert(array(
	    					'periode_id' => $last_periode,
	    					'fakultas_id' => $fakultas_id,
	    					'prodi_id' => $program->prodi_id,
	    					'program_id' => $program->program_id,
	    					'jadwal_id' => $value['idjdw'],
	    					'kode_matakuliah' => $value['kode'],
	    					'matakuliah' => $value['mk'],
	    					'sks' => $value['sks'],
	    					'semester' => $value['smt'],
	    					'kelas' => $value['kelas'] 
	    				));
	    			}
	    		}
	    	}
    	}
    	return "matakuliah periode ".$last_periode." pada fakultas ".$fakultas_id." sudah di update";
    }
}
