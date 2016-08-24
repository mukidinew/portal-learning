<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    protected $table = 'fakultas';

    public function get_fakultas()
    {
    	$last_periode = \App\Periode::max('id');
    	
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, 'http://203.24.50.30:9099/datasnap/rest/tservermethods1/fakultas');
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	$service = curl_exec($ch);

    	if($service === false)
    	{
    		return "Service Fakultas Sedang Gangguan";
    	}
    	else
    	{
    		$data = json_decode($service, TRUE);
    		foreach($data['result'][0] as $key => $value)
    		{
    			$hitung_fakultas = $this->where('fakultas_id', $value['idbagian'])
    									->where('periode_id', $last_periode)->count();
    			if($hitung_fakultas == 0)
    			{
    				$this->insert(array(
    					'periode_id' => $last_periode,
    					'fakultas_id' => $value['idbagian'],
    					'fakultas' => $value['fakultas']
    				));
    			}
    		}
    		return "Fakultas periode ".$last_periode." sudah di update";
    	}
    }
}
