<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function updateEmailMahasiswa(Request $request)
    {
        $nim = $request->nim;
        $email = $request->email;

        $mahasiswa = new \App\Mahasiswa;

        $mahasiswa->where('nim', $nim)->update(array('email_mahasiswa' => $email));

        return true;
    }
}
