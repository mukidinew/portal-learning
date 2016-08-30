<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    public function get_matakuliah_mahasiswa($fakultas_id)
    {
    	// $last_periode = \App\Periode::max('id');
    	$last_periode = 338;

    	$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://203.24.50.30:9099/datasnap/rest/tservermethods1/jadwalperiodefak/'.$last_periode.'/'.$fakultas_id);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);

			if($result == false)
			{
				return "Bad Request, Untan Service Down";
			}
			else
			{
				$data = json_decode($result);

				foreach($data->result[0] as $key => $value)
				{
					// Mengecek mahasiswa sudah ada belum di table mahasiswa
					$hitung_mahasiswa = $this->where('nim', $value->nim)->count();
					if($hitung_mahasiswa == 0)
					{
						$this->insert(array(
							'nim' => $value->nim,
							'nama_mahasiswa' => $value->nama
						));
					}

					// Mengecek matakuliah mahasiswa sudah ada belum di table matakuliah mahasiswa
					$hitung_matkul_mahasiswa = DB::table('matakuliah_mahasiswa')
																			->where('periode_id', $last_periode)
																			->where('jadwal_id', $value->idjadwal)
																			->where('nim', $value->nim)->count();

					if($hitung_matkul_mahasiswa == 0)
					{
						DB::table('matakuliah_mahasiswa')
							->insert(array(
									'periode_id' => $last_periode,
									'jadwal_id' => $value->idjadwal,
									'kode_matakuliah' => $value->kodemk,
									'nim' => $value->nim
						));
					}
				}
			}

			return "matakuliah Mahasiswa periode ".$last_periode." pada fakultas ".$fakultas_id." sudah di update";
    }
}
