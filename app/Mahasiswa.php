<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Mahasiswa extends Model
{
    public $data;
    private $serviceLogin = 'http://login.untan.ac.id/portal/api/service/';

    /* ============= E-learning ================= */
    public $token = 'e195e15add7e9370355f0416dfcc306d';
    public $domain = 'http://e-learning.untan.ac.id/learning';

	// public $token = '4a69f57b2efc3edaa9eb7dfee032aafc';
	// public $domain = 'http://e-learning.untan.dev/moodle';

    public $service = 'http://203.24.50.30:4444/Datasnap/Rest/Tservermethods1/loginmhs';

    public function ambil_service($username, $password)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->service.'/'.$username.'/x'.$password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        if($result === false)
        {
            // Jika service mati
            $this->data = array('idmhs' => -10);
        }
        else
        {
            $json = json_decode($result, TRUE);
            if(isset($json['error']))
            {
                $this->data = array('idmhs' => -10);
            }
            else if($json['result'][0]['idmhs'] > 0)
            {
                $this->data = array('idmhs' => $json['result'][0]['idmhs'],
                                    'nama' => $json['result'][0]['nama'],
                                    'username' => $json['result'][0]['username'],
                                    'password' => ltrim($json['result'][0]['passwd'], 'x'),
                                    'prodi' => $json['result'][0]['progdi']);
            }
            else if($json['result'][0]['idmhs'] == 0)
            {
                $this->data = array('idmhs' => $json['result'][0]['idmhs']);
            }
        }
    }

    // Cek User yang pernah akses E-Learning
    public function cek_mahasiswa($username, $password)
    {
        // Hitung Mahasiswa apakah terdaftar di DB
        $hitung = DB::table('ref_mahasiswa')->where('nim', '=', $username)->count();
        // jika belum terdaftar di DB
        if($hitung == 0)
        {
            $this->ambil_service($username, $password);
            // jika terdaftar di service
            if($this->data['idmhs'] > 0)
            {
                DB::table('ref_mahasiswa')
                    ->insert(array(
                        'nim'               => $this->data['username'],
                        'password'          => $this->data['password'],
                        'id_mhs'            => $this->data['idmhs'],
                        'nama'              => $this->data['nama'],
                        'prodi'             => $this->data['prodi']
                    ));
                return array('status'=>'sukses', 'pesan'=>'Berhasil update data mahasiswa');
            }
            // jika Service Mati
            else if($this->data['idmhs'] == -10)
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
            $user = DB::table('ref_mahasiswa')->where('nim', '=', $username)->first();
            // jika password dan nama masih kosong, maka cek service
            if($user->password == '' || $user->nama == '')
            {
                $this->ambil_service($username, $password);
                // jika terdaftar di service
                if($this->data['idmhs'] > 0)
                {
                    // di update password, nama, prodi di table ref_mahasiswa
                    DB::table('ref_mahasiswa')
                        ->where('nim', '=', $username)
                        ->update(array(
                            'password'          => $this->data['password'],
                            'nama'              => $this->data['nama'],
                            'prodi'             => $this->data['prodi']));
                    return array('status'=>'sukses', 'pesan'=>'Berhasil update data mahasiswa');
                }
                // jika Service Mati
                else if($this->data['idmhs'] == -10)
                {
                    return array('status'=>'gagal', 'pesan'=>'Service Siakad sedang Gangguan, Silahkan coba beberapa saat lagi');
                }
                 // jika password salah
                else
                {
                    return array('status'=>'gagal', 'pesan'=>'Cek lagi Password yang dimasukkan');
                }
            }
            // jika sudah punya password
            else
            {
                // jika password yang diinputkan dengan password yang didatabase sama
                if($password == $user->password)
                {
                    $this->data = array('idmhs' => $user->id_mhs,
                                        'username' => $user->nim,
                                        'password' => $user->password,
                                        'nama' => $user->nama,
                                        'prodi' => $user->prodi);
                    return array('status'=>'sukses', 'pesan'=>'Lanjut ke proses selanjutnya');
                }
                // jika password beda dengan database, maka di cek dengan service
                else
                {
                    $this->ambil_service($username, $password);
                    // Jika ditemukan di service, update password yang diinputkan
                    if($this->data['idmhs'] > 0)
                    {
                        DB::table('ref_mahasiswa')
                            ->where('nim', '=', $username)
                            ->update(array(
                                'password' => $password
                            ));
                        return array('status'=>'sukses', 'pesan'=>'Berhasil update data mahasiswa');
                    }
                    // jika Service Mati
                    else if($this->data['idmhs'] == -10)
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

    public function cek_email_mahasiswa($username)
    {

        $hitung_email = DB::table('email_mahasiswa')->where('nim', '=', $username)->count();

        if($hitung_email == 0)
        {
            $status = array('ada'=>0);
            return $status;
        }
        else
        {
            $email = DB::table('email_mahasiswa')->where('nim', '=', $username)->first();
            $status = array('ada'=>1, 'email'=>$email->email, 'password_email'=>$email->password);
            return $status;
        }
    }

    public function cek_moodle_mahasiswa($username)
    {
    	$moodle = DB::table('ref_mahasiswa')->where('nim', '=', $username)->first();

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

    /* Untuk memasukkan user kedalam e-learning */
    public function daftar_mahasiswa($username, $password, $email)
    {
        $functionname = 'core_user_create_users';
		$restformat = 'json';
		$serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

		$nama_array = explode(' ', session()->get('nama_mhs'));

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

        DB::table('ref_mahasiswa')
            ->where('nim', '=', $username)
            ->update(array('moodle_id' => $hasil[0]->id, 'password_moodle' => $password));

		return $hasil;
    }

    /* untuk update password login moodle */
    public function update_mahasiswa($userid, $password, $email)
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

    /* untuk memasukkan user tersebut role sebagai mahasiswa */
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

    public function get_makul_mahasiswa($username, $id_periode)
    {
        $matkul_moodle = DB::table('matakuliah_mahasiswa')
                            ->leftJoin('matakuliah', function($join) {
                                $join->on('matakuliah_mahasiswa.id_jadwal', '=', 'matakuliah.id_jadwal');
                            })
                            ->where('matakuliah_mahasiswa.nim_mahasiswa', '=', $username)
                            ->where('matakuliah_mahasiswa.id_periode', '=', $id_periode)
                            ->get();

        return $matkul_moodle;
    }

    /* ================= Ndak di pake Lagi ======================
    public function ambil_service_old($username, $password)
    {
        $context = stream_context_create(array(
            'http' => array('ignore_errors' => true),
        ));

        // menyesuaikan password
        $password = 'x'.$password;
        // ambil data dari servive siakad
        $json = file_get_contents('http://203.24.50.30:3333/Datasnap/Rest/Tservermethods1/loginmhs/'.$username.'/'.$password, false, $context);
        $json = json_decode($json, TRUE);

        if(isset($json['result'][0]))
        {
            $this->data = $json['result'][0];
        }
        else
        {
            $this->data['idmhs'] = -1;
        }
    }

    public function show_password_email($password)
    {
        $ubah = trim($password, 'xch');
        $ubah = trim($ubah, 'qyt');

        return $ubah;
    } */
}
