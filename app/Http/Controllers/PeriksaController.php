<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;

class PeriksaController extends Controller
{
  public function index()
  {
  	// ini masih statis, nanti kita pake yang dari service
    $periode_id = 341;

    if(session()->has('moodle_id') && session()->get('status') == 'mahasiswa')
    {
    	echo "halaman matakuliah mahasiswa";
    }
    else if(session()->has('moodle_id') && session()->get('status') == 'dosen')
    {
    	echo "halaman matakuliah dosen";
    }
    else
    {
      return view('login');
    }
  } 
  /* end of index method */

  public function verifikasi(Request $request)
  {
  	$username = $request->username;
    $password = $request->password;

    // cek apakah dosen / mahasiswa
    if(preg_match('/^[0-9]{1,}$/', $username))
    {
        return $this->dosen($username, $password);
    }
    else if(is_numeric(substr($username, 1, strlen($username))))
    {
        return $this->mahasiswa($username, $password);
    }
  } 
  /* end of verifikasi method */

  public function logout()
  {
      session()->flush();
      return redirect('/');
  }
  /* end of logout method */
}

?>