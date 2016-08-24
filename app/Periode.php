<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    protected $table = 'periode';

    public function get_periode()
    {
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, 'http://203.24.50.30:9099/datasnap/rest/tservermethods1/periodekuliah');
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	$service = curl_exec($ch);

    	if($service === false)
    	{
    		return "Service Periode Sedang Gangguan";
    	}
    	else
    	{
    		$data = json_decode($service, TRUE);
    		foreach($data['result'][0] as $key => $value)
    		{
    			$hitung_periode = $this->where('id', $value['idperiode'])->count();
    			if($hitung_periode == 0)
    			{
    				$this->insert(array(
    					'id' => $value['idperiode'],
    					'tahun_akademik' => $value['thakad']
    				));
    			}
    		}
    		return "Periode berhasil di update";
    	}
    }
}
