<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;

class PeriksaController extends Controller
{
  public function index()
  {
    return view('login');
  }

  public function verifikasi(Request $request)
  {
    $username = $request->username;
    $password = $request->password;

    // cek apakah dosen / mahasiswa
    if(preg_match('/^[0-9]{1,}$/', $username))
    {
      return $this->verifikasi_dosen($username, $password);
    }
    else if(is_numeric(substr($username, 1, strlen($username))))
    {
      return $this->verifikasi_mahasiswa($username, $password);
    }
    else
    {
      return redirect('/')->with('warning','Anda tidak terdaftar di service siakad');
    }
  }

  /* =================================================================== 
    Private Method 
  =================================================================== */

  private function verifikasi_mahasiswa($username, $password)
  {
    $mahasiswa = new \App\Mahasiswa;

    $cek_mahasiswa = $mahasiswa->cek_mahasiswa($username, $password);

    if($cek_mahasiswa)
    {
      session()->put('nim', $username);
      session()->put('password_siakad', $password);

      $cek_email_mahasiswa = $mahasiswa->cek_email_mahasiswa($username, $password);

      // jika belum punya email
      if(!$cek_email_mahasiswa)
      {
        session()->put('email', 'belum-ada');
        return redirect('registrasi-email');
      }
      // jika sudah punya email
      else
      {
        $cek_moodle_mahasiswa = $mahasiswa->cek_moodle_mahasiswa($username, $password);

        if($cek_moodle_mahasiswa)
        {
          echo "sudah lengkap semua lanjut ke login";
        }
      }
    }
    else
    {
      return redirect('/')->with('warning', 'Terjadi kesalahan sistem, cek lagi Username dan Password anda atau Service Siakad sedang mengalami gangguan');
    } 
  }
}


?>