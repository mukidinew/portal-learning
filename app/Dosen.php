<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Dosen extends Model
{
		protected $table = 'dosen';

		private $token = 'e195e15add7e9370355f0416dfcc306d';
  	private $domain = 'http://e-learning.untan.ac.id/learning';

  	public $timestamps = false;

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

		public function cek_service($service)
    {
    	$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $service);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);

			return $result;
    }

    public function cek_dosen($username, $password)
    {
    	$service = 'http://203.24.50.30:7475/Datasnap/Rest/Tservermethods1/logindosen/X'.$username.'/X'.$password;

    	$hitung_dosen = $this->where('nip', $username)->count();

    	// jika hitung dosen == 1 (ada);
    	if($hitung_dosen == 1)
    	{
    		$dosen = $this->where('nip', $username)->first();
    		// jika password == NULL
    		if($dosen->password == NULL)
    		{
    			$service = $this->cek_service($service);

    			if($service === false)
    			{
    				return false;
    			}
    			else
    			{
    				$data = json_decode($service, TRUE);

    				if(isset($data['error']))
						{
							return false;
						}
    				elseif($data['result'][0]['stat'] == 'aktif')
    				{
    					$this->where('nip', $username)->update(array(
    						'iden' => $data['result'][0]['iden'],
    						'dosen_id' => $data['result'][0]['iddosen'],
    						'password' => md5('X'.$password)
    					));

    					return true;
    				}
    				else
    				{
    					return false;
    				}
    			}
    		}
    		// jika password tidak sama dengan md5 password
    		elseif($dosen->password != md5('X'.$password))
    		{
    			$service = $this->cek_service($service);

    			if($service === false)
    			{
    				return false;
    			}
    			else
    			{
    				$data = json_decode($service, TRUE);

    				if(isset($data['error']))
						{
							return false;
						}
    				elseif($data['result'][0]['stat'] == 'aktif')
    				{
    					$this->where('nip', $username)->update(array(
    						'password' => md5('X'.$password)
    					));

    					return true;
    				}
    				else
    				{
    					return false;
    				}
    			}
    		}
    		// jika sudah ada di database dan password sama
    		elseif($dosen->password == md5('X'.$password))
    		{
    			return true;
    		}
    	}
    	// jika tidak ada di
    	else
    	{
    		return false;
    	}
    }

    public function cek_email_dosen($username, $password)
    {
    	$dosen = $this->where('nip', $username)->first();
    	// jika email dosen == NULL
    	if($dosen->email_dosen == NULL)
    	{
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }

    public function cek_moodle_dosen($username, $password)
    {
    	$dosen = $this->where('nip', $username)->first();

    	// jika moodle dosen == NULL
    	if($dosen->moodle_dosen_id == NULL)
    	{
    		// cek di moodlenya
    		$functionname = 'core_user_get_users';
	    	$restformat = 'json';
	    	$serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

	    	$user = array();
		    $user[0]['key'] = 'username';
		    $user[0]['value'] = $username;
		    
		    $params = array('criteria' => $user);

		    //url-ify the data for the POST
		    $field_string = http_build_query($params);

		    //open connection
		    $ch = curl_init();

		    //set the url, number of POST vars, POST data
		    curl_setopt($ch,CURLOPT_URL, $serverurl);
		    curl_setopt($ch,CURLOPT_POST, 1);
		    curl_setopt($ch,CURLOPT_POSTFIELDS, $field_string);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		    //execute post
		    $result = curl_exec($ch);

		    //close connection
		    curl_close($ch);

		    $hasil = json_decode($result);

		    // jika tidak ada di moodle maka daftarkan user
		    if(count($hasil->users) == 0)
		    {
		    	$this->daftar_moodle_dosen($dosen->nip, $password, $dosen->email_dosen, $dosen->nama_dosen);

		    	return true;
		    }
		    // jika sudah ada di moodle tapi belum ada di database
		    else
		    {
		    	$this->where('nip', $username)->update(array('moodle_dosen_id' => $hasil->users[0]->id));

		    	$this->update_moodle_dosen($hasil->users[0]->id, $password, $dosen->email_dosen);

		    	return true;
		    }
    	}
    	// jika sudah ada di moodle
    	else
    	{
    		$this->update_moodle_dosen($dosen->moodle_dosen_id, $password, $dosen->email_dosen);

		    return true;
    	}
    }

    public function daftar_moodle_dosen($nip, $password, $email_dosen, $nama_dosen)
    {
    	$functionname = 'core_user_create_users';
    	$restformat = 'json';
    	$serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

    	$nama_array = explode(' ', $nama_dosen);

    	$user = array();
	    $user[0]['username'] = strtolower($nip);
	    $user[0]['password'] = $password;
	    $user[0]['idnumber'] = $nip;
	    $user[0]['firstname'] = $nama_array[0];
	    $user[0]['email'] = $email_dosen;
	    array_shift($nama_array);
	    $user[0]['lastname'] = implode(' ', $nama_array);
	    $user[0]['country'] = 'ID';

	    $params = array('users' => $user);

	    //url-ify the data for the POST
	    $field_string = http_build_query($params);

	    //open connection
	    $ch = curl_init();

	    //set the url, number of POST vars, POST data
	    curl_setopt($ch,CURLOPT_URL, $serverurl);
	    curl_setopt($ch,CURLOPT_POST, 1);
	    curl_setopt($ch,CURLOPT_POSTFIELDS, $field_string);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	    //execute post
	    $result = curl_exec($ch);

	    //close connection
	    curl_close($ch);

	    $hasil = json_decode($result);

	    $this->assign_role_dosen($hasil[0]->id, 3);

	    $this->where('nip', $nip)->update(array('moodle_dosen_id' => $hasil[0]->id));
    }

    public function assign_role_dosen($userid, $roleid)
    {
    	// fungsi untuk memasukkan role id di moodle
	    $functionname = 'core_role_assign_roles';
	    $restformat = 'json';

	    $serverurl = $this->domain."/webservice/rest/server.php?wstoken=".$this->token."&wsfunction=".$functionname."&moodlerestformat=".$restformat;

	    $data_array = array();
	    $data_array[0]['roleid'] = $roleid;
	    $data_array[0]['userid'] = $userid;

	    $params = array('users'=>$data_array);

	    //url-ify the data for the POST
	    $field_string = http_build_query($params);

	    // open connection
	    $ch = curl_init();

	    // set the url, number of POST vars, POST data
	    curl_setopt($ch, CURLOPT_URL, $serverurl);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	    // excute post
	    $result = curl_exec($ch);

	    // close connection
	    curl_close($ch);
    }

    public function update_moodle_dosen($userid, $password, $email)
    {
    	$functionname = 'core_user_update_users';
	    $restformat = 'json';
	    $serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

	    $user = array();
	    $user[0]['id'] = $userid;
	    $user[0]['password'] = $password;
	    $user[0]['email'] = $email;

	    $params = array('users' => $user);

	    //url-ify the data for the POST
	    $field_string = http_build_query($params);

	    //open connection
	    $ch = curl_init();

	    //set the url, number of POST vars, POST data
	    curl_setopt($ch,CURLOPT_URL, $serverurl);
	    curl_setopt($ch,CURLOPT_POST, 1);
	    curl_setopt($ch,CURLOPT_POSTFIELDS, $field_string);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	    //execute post
	    $result = curl_exec($ch);

	    //close connection
	    curl_close($ch);
    }

    public function get_profil_dosen($nip)
    {
    	$profil = DB::table('dosen')->where('nip', $nip)->first();
    	return $profil;
    }

    public function get_matkul_dosen($nip)
    {
    	$periode_id = \App\Periode::max('id');
	  	$matkul = DB::table('matakuliah_dosen')
	  								->select('*', 'matakuliah_dosen.id as matakuliah_dosen_id', 'matakuliah.id as matakuliah_id')
	  								->leftJoin('matakuliah', 'matakuliah_dosen.jadwal_id', '=', 'matakuliah.jadwal_id')
	  								->leftJoin('prodi', function($join) {
	  									$join->on('matakuliah.prodi_id', '=', 'prodi.prodi_id')
	  											 ->on('matakuliah.periode_id', '=', 'prodi.periode_id');
	  								})
	  								->leftJoin('program', function($join) {
	  									$join->on('matakuliah.program_id', '=', 'program.program_id')
	  											 ->on('matakuliah.prodi_id', '=', 'program.prodi_id')
	  											 ->on('matakuliah.periode_id', '=', 'program.periode_id');
	  								})
	  								->where('matakuliah.periode_id', $periode_id)
	  								->where('nip', $nip)
	  								->get();
	  	return $matkul;
    }
}
