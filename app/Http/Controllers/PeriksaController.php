<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;

class PeriksaController extends Controller
{
  public function index()
  {
    if(session()->get('status') == 'mahasiswa')
    {
      $mahasiswa = new \App\Mahasiswa;
      $nim = session()->get('nim');

      $mahasiswa_matkul = $mahasiswa->get_matkul_mahasiswa($nim);
      $mahasiswa_profil = $mahasiswa->get_profil_mahasiswa($nim);

      // print_r($mahasiswa_matkul);

      $data['mahasiswa_matkul'] = $mahasiswa_matkul;
      $data['mahasiswa_profil'] = $mahasiswa_profil;

      return view('elearning-mahasiswa', $data);
    }
    elseif(session()->get('status') == 'dosen')
    {
      $dosen = new \App\Dosen;
      $nip = session()->get('nip');

      $dosen_matkul = $dosen->get_matkul_dosen($nip);
      $dosen_profil = $dosen->get_profil_dosen($nip);

      // print_r($dosen_matkul);

      $data['dosen_profil'] = $dosen_profil;
      $data['dosen_matkul'] = $dosen_matkul;

      return view('elearning-dosen', $data);
    }
    else
    {
      return view('login');
    }
    
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

  public function logout()
  {
      session()->flush();
      return redirect('/');
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
      session()->put('status', 'mahasiswa');
      session()->put('nim', $username);
      session()->put('password_siakad', $password);

      $cek_email_mahasiswa = $mahasiswa->cek_email_mahasiswa($username, $password);

      // jika belum punya email
      if(!$cek_email_mahasiswa)
      {
        // session()->put('email', 'belum-ada');
        // return redirect('registrasi-email');
        return redirect('http://tik.untan.ac.id/registrasi-email/?nim='.$username);
      }
      // jika sudah punya email
      else
      {
        $cek_moodle_mahasiswa = $mahasiswa->cek_moodle_mahasiswa($username, $password);

        if($cek_moodle_mahasiswa)
        {
          return redirect('/');
        }
      }
    }
    else
    {
      return redirect('/')->with('warning', 'Terjadi kesalahan sistem, cek lagi Username dan Password anda atau Service Siakad sedang mengalami gangguan');
    } 
  }

  private function verifikasi_dosen($username, $password)
  {
    $dosen = new \App\Dosen;

    $cek_dosen = $dosen->cek_dosen($username, $password);

    if($cek_dosen)
    {
      session()->put('status', 'dosen');
      session()->put('nip', $username);
      session()->put('password_siakad', $password);

      $cek_email_dosen = $dosen->cek_email_dosen($username, $password);

      // jika belum punya email
      if(!$cek_email_dosen)
      {
        session()->put('email-dosen', 'belum-ada');
        return redirect('validasi-email-dosen');
      }
      // jika sudah punya email
      else
      {
        $cek_moodle_dosen = $dosen->cek_moodle_dosen($username, $password);

        if($cek_moodle_dosen)
        {
          return redirect('/');
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