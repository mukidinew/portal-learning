<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Mahasiswa extends Model
{
  private $service = 'http://203.24.50.30:4444/Datasnap/Rest/Tservermethods1/loginmhs';
  private $token = 'e195e15add7e9370355f0416dfcc306d';
  private $domain = 'http://e-learning.untan.ac.id/learning';

  private function ambil_service($username, $password)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->service.'/'.$username.'/x'.$password);
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
      // jika mahasiswa tidak aktif / tidak terdaftar
      else if($json['result'][0]['idmhs'] == 0)
      {
        return array('status'=>'gagal', 'pesan'=>'Cek lagi Password yang dimasukkan');
      }
      // jika mahasiswa aktif 
      else if($json['result'][0]['idmhs'] > 0)
      {
        return array('status' => 'sukses',
                      'idmhs' => $json['result'][0]['idmhs'],
                      'nama' => $json['result'][0]['nama'],
                      'username' => $json['result'][0]['username'],
                      'password' => ltrim($json['result'][0]['passwd'], 'x'),
                      'prodi' => $json['result'][0]['progdi']);
      }
    }
  }

  public function cek_mahasiswa($username, $password)
  {
    // Hitung Mahasiswa apakah terdaftar di DB
    $hitung = DB::table('ref_mahasiswa')->where('nim', '=', $username)->count();
    // jika belum terdaftar di DB
    if($hitung == 0)
    {
      $data = $this->ambil_service($username, $password);
      if($data['status'] == 'sukses')
      {
        DB::table('ref_mahasiswa')
            ->insert(array(
                'nim'      => $data['username'],
                'password' => $data['password'],
                'id_mhs'   => $data['idmhs'],
                'nama'     => $data['nama'],
                'prodi'    => $data['prodi']
            ));
      }
      return $data;
    }
    // jika terdaftar di DB
    else
    {
      $user = DB::table('ref_mahasiswa')->where('nim', '=', $username)->first();
      // jika password masih kosong
      if($user->password == '' || $user->nama == '')
      {
        $data = $this->ambil_service($username, $password);
        DB::table('ref_mahasiswa')
          ->where('nim', '=', $username)
          ->update(array(
              'password' => $data['password'],
              'nama'     => $data['nama'],
              'prodi'    => $data['prodi']
          ));
        return $data;
      }
      // jika  password tidak sama
      else if($user->password != $password)
      {
        $data = $this->ambil_service($username, $password);
        DB::table('ref_mahasiswa')
          ->where('nim', '=', $username)
          ->update(array(
              'password' => $password
          ));
        return $data;   
      }
      // jika user dan password sama
      else
      {
        return array('status' => 'sukses',
                      'idmhs' => $user->id_mhs,
                      'nama' => $user->nama,
                      'username' => $user->nim,
                      'password' => $user->password,
                      'prodi' => $user->prodi);
      }
    }
  }
  /* end cek_mahasiswa method */

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
  /* end cek_email_mahasiswa method */

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
  /* end cek_moodle_mahasiswa method */

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
  /* end daftar mahasiswa moodle method */

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
  /* end update password method */

}

?>