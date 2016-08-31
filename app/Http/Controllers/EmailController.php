<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class EmailController extends Controller
{
  public function createEmail()
  {
    if(session()->has('email') && session()->get('email') == 'belum-ada')
    {
          $username = session()->get('username');
      return view('registrasi-email')->with('username',$username);
    }
    else {
      return redirect('/');
    }
  }

  public function insertEmail(Request $request)
  {
    $rules = array(
        'first_name'        => 'required',
        'last_name'         => 'required',
        'email'             => 'required',
        'password'          => 'required|min:9',
        'password_confirm'  => 'required|same:password'
    );

    $messages = [
        'required'  => 'Inputan Wajib Diisi',
        'same'      => 'Password harus sama',
        'min'       => 'Password minimal 9 Karakter'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if($validator->fails()) 
    {
        return back()->withErrors($validator)
                     ->withInput();
    }
    else
    {
      $nim = session()->get('nim');
      $password_siakad = session()->get('password_siakad');
      $email = strtolower($request->email.'@student.untan.ac.id');
      $password = $request->password;
      $first_name = $request->first_name;
      $last_name = $request->last_name;

      $cek_email_to_google = json_decode($this->cekEmailGoogle($email));

      // Jika Belum email ada di google
      if(empty($cek_email_to_google->users))
      {
        // =========== Buat Email di google ==============
        $result_google = $this->insert_to_google($email,$password,$first_name,$last_name);

        if($result_google->id)
        {
          $mahasiswa = new \App\Mahasiswa;

          $mahasiswa->where('nim', $nim)->update(array('email_mahasiswa' => $email));

          $cek_moodle_mahasiswa = $mahasiswa->cek_moodle_mahasiswa($nim, $password_siakad);

          if($cek_moodle_mahasiswa)
          {
            echo "sudah lengkap semua lanjut ke login";
          }
        }
      }
      // jika email sudah terpakai
      else
      {
        return back()->with('warning', 'Email Sudah digunakan oleh mahasiswa lain')
                    ->withInput();
      }
    }
  }

  public function cekEmailGoogle($email)
  {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $token_url = 'https://accounts.google.com/o/oauth2/token';

    $postdata = "refresh_token=".urlencode("1/36COUpoxljLVGW-77vUho80p0g3XJAfBbGHnEj5HKsI");
    $postdata .= "&client_id=".urlencode("1009809002386-r0pavvupg2s9lcql75hrh2qj60v8iig9.apps.googleusercontent.com");
    $postdata .= "&grant_type=".urlencode("refresh_token");
    $postdata .= "&client_secret=".urlencode("-wsVkUlht5a9S8px1zzRrl8T");

    $post_array = array(
        "refresh_token"=>urlencode("1/36COUpoxljLVGW-77vUho80p0g3XJAfBbGHnEj5HKsI"),
        "client_id"=>urlencode("1009809002386-r0pavvupg2s9lcql75hrh2qj60v8iig9.apps.googleusercontent.com"),
        "grant_type"=>urlencode("refresh_token"),
        "client_secret"=>urlencode("-wsVkUlht5a9S8px1zzRrl8T")
    );

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    // Option to Return the Result, rather than just true/false
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    // Perform the request, and save content to $result
    $token_result = json_decode(curl_exec($ch));
    // Close the cURL resource, and free up system resources!
    curl_close($ch);
    $token =  $token_result->access_token;

    // periksa email di server google
    $url = 'https://www.googleapis.com/admin/directory/v1/users?key=AIzaSyAmseD3oDv7-icllWSpZA42JV1_5h8kapo&customer=my_customer&query=email:'.$email;

    // Initialize cURL session
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer '.$token
    ));
    // Option to Return the Result, rather than just true/false
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $result = curl_exec($ch);
    // Perform the request, and save content to $result
    // $result = json_decode(curl_exec($ch));
    // Close the cURL resource, and free up system resources!
    curl_close($ch);

    return $result;
  }

  private function insert_to_google($email,$password,$first_name,$last_name)
  {
    $token_url = 'https://accounts.google.com/o/oauth2/token';

    $postdata = "refresh_token=".urlencode("1/36COUpoxljLVGW-77vUho80p0g3XJAfBbGHnEj5HKsI");
    $postdata .= "&client_id=".urlencode("1009809002386-r0pavvupg2s9lcql75hrh2qj60v8iig9.apps.googleusercontent.com");
    $postdata .= "&grant_type=".urlencode("refresh_token");
    $postdata .= "&client_secret=".urlencode("-wsVkUlht5a9S8px1zzRrl8T");

    $post_array = array(
        "refresh_token"=>urlencode("1/36COUpoxljLVGW-77vUho80p0g3XJAfBbGHnEj5HKsI"),
        "client_id"=>urlencode("1009809002386-r0pavvupg2s9lcql75hrh2qj60v8iig9.apps.googleusercontent.com"),
        "grant_type"=>urlencode("refresh_token"),
        "client_secret"=>urlencode("-wsVkUlht5a9S8px1zzRrl8T")
    );

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    // Option to Return the Result, rather than just true/false
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    // Perform the request, and save content to $result
    $token_result = json_decode(curl_exec($ch));
    // Close the cURL resource, and free up system resources!
    curl_close($ch);
    $token =  $token_result->access_token;

    $url = 'https://www.googleapis.com/admin/directory/v1/users?key=AIzaSyAmseD3oDv7-icllWSpZA42JV1_5h8kapo';

    $jsondata = '{
        "name":{
            "familyName":"'.$last_name.'",
            "givenName":"'.$first_name.'"
        },
        "password":"'.$password.'",
        "primaryEmail":"'.$email.'"
    }';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer '.$token,
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsondata);
    // Option to Return the Result, rather than just true/false
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    // Perform the request, and save content to $result
    $result = json_decode(curl_exec($ch));
    // Close the cURL resource, and free up system resources!
    curl_close($ch);

    return $result;
  }

  public function validasiEmailDosen()
  {
    if(session()->has('email-dosen') && session()->get('email-dosen') == 'belum-ada')
    {
      $username = session()->get('username');
      $password = session()->get('password');
      return view('validasi-email-dosen')->with('username',$username)->with('password', $password);
    }
    else
    {
      return redirect('/');
    }
  }

  public function prosesValidasiEmailDosen(Request $request)
  {
    $email = strtolower($request->emaildosen);
    $username = $request->username;
    $password = $request->password;

    DB::table('email_dosen')->where('nip', '=', $username)->update(array('email'=>$email));

    $dosen = new Dosen;
    $cek_moodle = $dosen->cek_moodle_dosen($username);

    if($cek_moodle['ada'] == 0)
    {
      $daftar = $dosen->daftar_dosen($username, $password, $email);
      if(isset($daftar[0]->id))
      {
        $dosen->assign_role_dosen($daftar[0]->id, 3);

        session()->put('moodle_id', $daftar[0]->id);
        session()->put('status', 'dosen');
        return redirect('/');
      }
    }
    elseif($cek_moodle['ada'] == 1)
    {
      session()->put('moodle_id', $cek_moodle['moodle_id']);
      session()->put('status', 'dosen');

      return redirect('/');
    }
  }

}

?>