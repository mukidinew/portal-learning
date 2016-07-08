<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Dosen extends Model
{
    public $data;
    private $serviceLogin = 'http://login.untan.ac.id/portal/api/service/';

    /* ============= E-learning ================= */
    public $token = 'e195e15add7e9370355f0416dfcc306d';
	public $domain = 'http://e-learning.untan.ac.id/learning';

	// public $token = '4a69f57b2efc3edaa9eb7dfee032aafc';
	// public $domain = 'http://e-learning.untan.dev/moodle';

	public $service = 'http://203.24.50.30:4444/Datasnap/Rest/Tservermethods1/logindosen';

	public function ambil_service($username, $password)
	{
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->service.'/X'.$username.'/X'.$password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if($result === false)
        {
            // Jika service mati
            $this->data = array('stat' => 'error_service');
        }
        else
        {
            $json = json_decode($result, TRUE);
            if(isset($json['error']))
            {
                $this->data = array('stat' => 'error_service');
            }
            else
            {
            	$this->data = $json['result'][0];
            }
        }
	}

	// Cek User yang pernah akses E-Learning
	public function cek_dosen($username, $password)
	{
		// Hitung Dosen apakah terdaftar di DB
		$hitung = DB::table('ref_dosen')->where('nip', '=', $username)->count();
		// Jika belum terdaftar di DB
		if($hitung == 0)
		{
			$this->ambil_service($username, $password);
			// Jika pada service ditemukan
			if($this->data['stat'] == 'aktif')
			{
				DB::table('ref_dosen')
					->insert(array(
						'nip'				=> $username,
						'password'			=> $password,
						'id_dosen'			=> $this->data['iddosen'],
						'nama'				=> $this->data['nama']
					));
				return array('status'=>'sukses', 'pesan'=>'Berhasil insert data dosen');
			}
			// jika Service Mati
			else if($this->data['stat'] == 'error_service')
			{
                return array('status'=>'gagal', 'pesan'=>'Service Siakad sedang Gangguan, Silahkan coba beberapa saat lagi');
			}
			// jika password salah
			else
			{
                return array('status'=>'gagal', 'pesan'=>'Cek lagi Password yang dimasukkan');
			}
		}
		// Jika terdaftar di DB
		else
		{
			$user = DB::table('ref_dosen')->where('nip', '=', $username)->first();
			// jika password dan nama masih kosong
			if($user->password == '' || $user->nama == '')
			{
				$this->ambil_service($username, $password);
				// Jika pada service ditemukan
				if($this->data['stat'] == 'aktif')
				{
					// Update data Dosen
					DB::table('ref_dosen')
						->where('nip', '=', $username)
						->update(array(
							'password'			=> $password,
							'nama'				=> $this->data['nama']
						));
					return array('status'=>'sukses', 'pesan'=>'Berhasil Update data Dosen');
				}
				// jika Service Mati
				else if($this->data['stat'] == 'error_service')
				{
	                return array('status'=>'gagal', 'pesan'=>'Service Siakad sedang Gangguan, Silahkan coba beberapa saat lagi');
				}
				// jika password salah
				else
				{
	                return array('status'=>'gagal', 'pesan'=>'Cek lagi Password yang dimasukkan');
				}
			}
			// Jika sudah punya password
			else
			{
				// Jika password benar
				if($password == $user->password)
				{
					$this->data = array('username' => $user->nip,
										'nama' => $user->nama);
					return array('status' => 'sukses', 'pesan' => 'Lanjut ke proses selanjutnya');
				}
				// jika password di database tidak sama
				else
				{
					$this->ambil_service($username, $password);
					// Jika pada service ditemukan
					if($this->data['stat'] == 'aktif')
					{
						// Update password Dosen
						DB::table('ref_dosen')
							->where('nip', '=', $username)
							->update(array(
								'password'	=> $password,
							));
						return array('status'=>'sukses', 'pesan'=>'Berhasil Update data Dosen');
					}
					// jika Service Mati
					else if($this->data['stat'] == 'error_service')
					{
		                return array('status'=>'gagal', 'pesan'=>'Service Siakad sedang Gangguan, Silahkan coba beberapa saat lagi');
					}
					// jika password salah
					else
					{
		                return array('status'=>'gagal', 'pesan'=>'Cek lagi Password yang dimasukkan');
					}
				}
			}
		}
	}

    public function cek_email_dosen($username)
	{
		$hitung_user_email = DB::table('email_dosen')->where('nip', '=', $username)->count();

		if($hitung_user_email == 0) 
		{
			$status = array('ada'=>-1);
			return $status;
		}
		else
		{
			$email = DB::table('email_dosen')->where('nip', '=', $username)->first();

	        if($email->email == '')
	        {
	            $status = array('ada'=>0);
	            return $status;
	        }
	        else
	        {
	            $email = DB::table('email_dosen')->where('nip', '=', $username)->first();
	            $status = array('ada'=>1, 'email'=>$email->email);
	            return $status;
	        }
		}
		
	}

	public function cek_moodle_dosen($username)
	{
		$jml = DB::table('ref_dosen')->where('nip', '=', $username)->count();
		echo $username;
		die();
		if($jml == 0)
		{
			$status = array('ada'=>0, 'moodle_id'=>0);
			return $status;
		}
		else
		{
			$moodle = DB::table('ref_dosen')->where('nip', '=', $username)->first();
			$status = array('ada'=>1, 'moodle_id'=>$moodle->moodle_id);
			return $status;
		}
	}

	public function daftar_dosen($username, $password, $email)
	{
		$data_profil = file_get_contents('http://203.24.50.30:4444/Datasnap/Rest/Tservermethods1/logindosen/X'.$username.'/X'.$password, false);
		$data_profil = json_decode($data_profil);

		$functionname = 'core_user_create_users';
		$restformat = 'json';
		$serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

		$nama_array = explode(' ', $data_profil->result[0]->nama);

		$user = array();
		$user[0]['username'] = strtolower($username);
		$user[0]['password'] = $password;
		$user[0]['idnumber'] = $username;
		$user[0]['firstname'] = $nama_array[0];
		$user[0]['email'] = $email;
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

		DB::table('ref_dosen')
            ->where('nip', '=', $username)
            ->update(array('moodle_id' => $hasil[0]->id, 'password_moodle' => $password));

		return $hasil;
	}

	/* untuk update password login moodle */
    public function update_dosen($userid, $password, $email)
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

	public function get_makul_dosen($username, $id_periode) 
	{
		$matkul_moodle = DB::table('matakuliah_dosen')
							->leftJoin('matakuliah', function($join) {
								$join->on('matakuliah_dosen.id_jadwal', '=', 'matakuliah.id_jadwal');
							})
							->where('matakuliah_dosen.nip', '=', $username)
							->where('matakuliah_dosen.id_periode', '=', $id_periode)
							->get();

		return $matkul_moodle;
	}
}
