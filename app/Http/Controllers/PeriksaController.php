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
}

?>