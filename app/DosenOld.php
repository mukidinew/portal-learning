<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Dosen extends Model
{
	private $service = 'http://203.24.50.30:4444/Datasnap/Rest/Tservermethods1/logindosen';
  private $token = 'e195e15add7e9370355f0416dfcc306d';
  private $domain = 'http://e-learning.untan.ac.id/learning';

  private function ambil_service($username, $password)
  {
  	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->service.'/X'.$username.'/X'.$password);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);

    if($result === false)
    {
    	// Jika service mati
      return array('status'=>'gagal', 'pesan'=>'Service Siakad sedang Gangguan, Silahkan coba beberapa saat lagi');
    }
    else
    {
    	$json = json_decode($result, TRUE);

      // jika error too many connection
      if(isset($json['error']))
      {
        return array('status'=>'gagal', 'pesan'=>'Service Siakad sedang Gangguan, Silahkan coba beberapa saat lagi');
      }
      // jika dosen aktif
      else if($json['result'][0]['stat'] == 'aktif')
      {
      	return array('status' => 'sukses',
                      'iddosen' => $json['result'][0]['iddosen'],
                      'nama' => $json['result'][0]['nama'],
                      'username' => $username,
                      'password' => $password
                     );
      }
      // jika salah password atau error lain
      else
      {
      	return array('status'=>'gagal', 'pesan'=>'Cek lagi Password yang dimasukkan');
      }
    }
  }
  /* end cek_service method */

  public function cek_dosen($username, $password)
  {
  	// Hitung Dosen apakah terdaftar di DB
  	$hitung = DB::table('ref_dosen')->where('nip', '=', $username)->count();
  	// jika belum terdaftar di DB
  	if($hitung == 0)
  	{
  		$data = $this->ambil_service($username, $password);
  		if($data['status'] == 'sukses')
  		{
  			DB::table('ref_dosen')
  				->insert(array(
  					'nip' 			=> $data['username'],
  					'password'	=> $data['password'],
  					'id_dosen'	=> $data['iddosen'],
  					'nama' 			=> $data['nama']
  				));
  		}
  		return $data;
  	}
  	// jika terdaftar di DB
  	else
  	{
  		$user = DB::table('ref_dosen')->where('nip', '=', $username)->first();
  		// jika password masih kosong
  		if($user->password == '' || $user->nama == '')
  		{
  			$data = $this->ambil_service($username, $password);
  			if($data['status'] == 'sukses')
        {
	  			DB::table('ref_dosen')
	  				->where('nip', '=', $username)
	  				->update(array(
	  					'password' 	=> $data['password'],
	  					'nama' 			=> $data['nama'] 
	  				));
	  		}
  			return $data;
  		}
  		// jika password tidak sama
  		else if($user->password != $password)
  		{
  			$data = $this->ambil_service($username, $password);
  			if($data['status'] == 'sukses')
  			{
  				DB::table('ref_dosen')
  					->where('nip', '=', $username)
  					->update(array(
  						'password' => $password
  					));
  			}
  			return $data;
  		}
  		else
  		{
  			return array('status' => 'sukses',
  										'iddosen' => $user->id_dosen,
  										'nama' => $user->nama,
  										'username' => $user->nip,
  										'password' => $user->password);
  		}
  	}
  }
  /* end cek_dosen method */

  public function cek_email_dosen($username)
  {
  	$hitung_user_email = DB::table('email_dosen')->where('nip', '=', $username)->count();

  	// jika tidak terdaftar di DB 
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
		$moodle = DB::table('ref_dosen')->where('nip', '=', $username)->first();

		if($moodle->moodle_id == '')
		{
			$status = array('ada'=>0,'moodle_id'=>0);
      return $status;
		}
		else
		{
			$status = array('ada'=>1,'moodle_id'=>$moodle->moodle_id, 'password'=>$moodle->password);
      return $status;
		}
	}

	public function daftar_dosen($username, $password, $email)
	{
		// $data_profil = file_get_contents('http://203.24.50.30:4444/Datasnap/Rest/Tservermethods1/logindosen/X'.$username.'/X'.$password, false);
		// $data_profil = json_decode($data_profil);

		$functionname = 'core_user_create_users';
		$restformat = 'json';
		$serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

		// $nama_array = explode(' ', $data_profil->result[0]->nama);
    $nama_array = explode(' ', session()->get('nama_dosen'));

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

?>