<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Dosen extends Model
{
		protected $table = 'dosen';

		public function get_matakuliah_dosen($fakultas_id)
		{
			$last_periode = \App\Periode::max('id');

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://203.24.50.30:9099/datasnap/rest/tservermethods1/jadwaldosenperiodefak/'.$last_periode.'/'.$fakultas_id);
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
					// Mengecek dosen sudah ada belum di table dosen
					$hitung_dosen = $this->where('nip', $value->nip)->count();
					if($hitung_dosen == 0)
					{
						$this->insert(array(
							'nip' => $value->nip,
							'nama_dosen' => $value->nama,
							'gelar_depan' => $value->gelardepan,
							'gelar_belakang' => $value->gelarbelakang
						));
					}

					// Mengecek matakuliah dosen sudah ada belum di table matakuliah dosen
					$hitung_matkul_dosen = DB::table('matakuliah_dosen')
																	->where('periode_id', $last_periode)
																	->where('jadwal_id', $value->idjadwal)
																	->where('nip', $value->nip)->count();
					if($hitung_matkul_dosen == 0)
					{
						DB::table('matakuliah_dosen')->insert(array(
							'periode_id' => $last_periode,
							'jadwal_id' => $value->idjadwal,
							'kode_matakuliah' => $value->kodemk,
							'nip' => $value->nip
						));
					}
				}
			}

			return "matakuliah Dosen periode ".$last_periode." pada fakultas ".$fakultas_id." sudah di update";
		}
}
