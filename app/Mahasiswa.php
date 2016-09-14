<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    private $token = 'e195e15add7e9370355f0416dfcc306d';
  	private $domain = 'http://e-learning.untan.ac.id/learning';

    public $timestamps = false;

    public function get_matakuliah_mahasiswa($fakultas_id)
    {
    	$last_periode = \App\Periode::max('id');

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

						echo $value->nim;
					}
				}
			}

			return "matakuliah Mahasiswa periode ".$last_periode." pada fakultas ".$fakultas_id." sudah di update";
    }

    public function cek_service($service)
    {
    	$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $service);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);

			return $result;
    }

    public function cek_mahasiswa($username, $password)
    {
    	$service = 'http://192.168.32.14:7475/Datasnap/Rest/Tservermethods1/loginmhs/'.$username.'/x'.$password;

    	$hitung_mahasiswa = $this->where('nim', $username)->count();

    	// jika hitung == 1 (ada)
    	if($hitung_mahasiswa == 1)
    	{
    		$mahasiswa = $this->where('nim', $username)->first();
    		// jika password == NULL
    		if($mahasiswa->password == NULL)
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
							false;
						}
						else if($data['result'][0]['idmhs'] > 0)
						{
							$this->where('nim', $username)->update(array(
								'iden' => $data['result'][0]['iden'],
								'mahasiswa_id' => $data['result'][0]['idmhs'],
								'password' => md5('x'.$password)
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
    		elseif($mahasiswa->password != md5('x'.$password))
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
							return redirect('/')->with('warning', 'Service Siakad sedang mengalami gangguan');
						}
						else if($data['result'][0]['idmhs'] > 0)
						{
							$this->where('nim', $username)->update(array(
								'password' => md5('x'.$password)
							));

							return true;
						}
						else
						{
							return false;
						}
    			}
    		}
    		// jika betul semua
    		else
    		{
    			return true;
    		}
    	}
    	// jika tidak ada 
    	else
    	{
    		return false;
    	}
    }

    public function cek_email_mahasiswa($username, $password)
    {
    	$mahasiswa = $this->where('nim', $username)->first();
    	// jika email mahasiswa == NULL
    	if($mahasiswa->email_mahasiswa == NULL)
    	{
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }

    public function cek_moodle_mahasiswa($username, $password)
    {
    	$mahasiswa = $this->where('nim', $username)->first();
    	
    	// jika moodle mahasiswa == NULL
    	if($mahasiswa->moodle_mahasiswa_id == NULL)
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
		    	$this->daftar_moodle_mahasiswa($mahasiswa->nim, $password, $mahasiswa->email_mahasiswa, $mahasiswa->nama_mahasiswa);

		    	return true;
		    }
		    // jika sudah ada di moodle tapi belum ada di database
		    else
		    {
		    	$this->where('nim', $username)->update(array('moodle_mahasiswa_id' => $hasil->users[0]->id));

		    	$this->update_moodle_mahasiswa($hasil->users[0]->id, $password, $mahasiswa->email_mahasiswa);

		    	return true;
		    }
    	}
    	// jika sudah ada di moodle
    	else
    	{
    		$this->update_moodle_mahasiswa($mahasiswa->moodle_mahasiswa_id, $password, $mahasiswa->email_mahasiswa);

		    return true;
    	}
    }

    public function daftar_moodle_mahasiswa($nim, $password, $email_mahasiswa, $nama_mahasiswa)
    {

    	$functionname = 'core_user_create_users';
    	$restformat = 'json';
    	$serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

    	$nama_array = explode(' ', $nama_mahasiswa);

    	$user = array();
	    $user[0]['username'] = strtolower($nim);
	    $user[0]['password'] = $password;
	    $user[0]['idnumber'] = $nim;
	    $user[0]['firstname'] = $nama_array[0];
	    $user[0]['email'] = $email_mahasiswa;
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

	    $this->assign_role_mahasiswa($hasil[0]->id, 5);

	    $this->where('nim', $nim)->update(array('moodle_mahasiswa_id' => $hasil[0]->id));

    }

    public function assign_role_mahasiswa($userid, $roleid)
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

    public function update_moodle_mahasiswa($userid, $password, $email)
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

	  public function get_matkul_mahasiswa($nim)
	  {
	  	$periode_id = \App\Periode::max('id');
	  	$matkul = DB::table('matakuliah_mahasiswa')
	  					->leftJoin('matakuliah', 'matakuliah_mahasiswa.jadwal_id', '=', 'matakuliah.jadwal_id')
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
							->where('nim', $nim)
							->get();
	  	return $matkul;
	  }

	  public function get_profil_mahasiswa($nim)
	  {
	  	$profil = DB::table('mahasiswa')->where('nim', $nim)->first();
    	return $profil;
	  }
}
