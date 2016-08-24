<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'program';

    public function get_program($fakultas_id)
    {
    	$last_periode = \App\Periode::max('id');

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, 'http://203.24.50.30:9099/datasnap/rest/tservermethods1/prodi_jadwal/'.$fakultas_id.'/'.$last_periode);
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
    			$hitung_program = $this->where('periode_id', $last_periode)
    								   ->where('prodi_id', $value['idprodi'])
    								   ->where('program_id', $value['idprog'])->count();

    			if($hitung_program == 0)
    			{
    				$this->insert(array(
    					'periode_id' => $last_periode,
    					'fakultas_id' => $fakultas_id,
    					'prodi_id' => $value['idprodi'],
    					'program_id' => $value['idprog'],
    					'program' => $value['prog']
    				));
    			}
    		}
    		return "Program periode ".$last_periode." pada fakultas ".$fakultas_id." sudah di update"; 
    	}
    }
}
