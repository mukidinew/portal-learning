<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Mahasiswa;
use App\Dosen;
use App\ElearningMatakuliah;

class PeriksaController extends Controller
{
    public function index()
    {
        $id_periode = 341;

        if(session()->has('moodle_id') && session()->get('status') == 'mahasiswa')
        {
            $username = session()->get('username');
            $password = session()->get('password');

            $email = session()->get('email');
            $password_email = session()->get('password_email');
            $moodle_id = session()->get('moodle_id');

            $mahasiswa = new Mahasiswa;
            $matkul_moodle = $mahasiswa->get_makul_mahasiswa($username, $id_periode);

            // print_r($matkul_moodle);
            return view('elearning-mahasiswa')->with('username', $username)
                                              ->with('password', $password)
                                              ->with('email', $email)
                                              ->with('password_email', $password_email)
                                              ->with('matkul_moodle',$matkul_moodle);
        }
        else if(session()->has('moodle_id') && session()->get('status') == 'dosen')
        {
            $username = session()->get('username');
            $password = session()->get('password');

            $moodle_id = session()->get('moodle_id');

            $dosen = new Dosen;
            $matkul_moodle = $dosen->get_makul_dosen($username, $id_periode);

            // print_r($matkul_moodle);
            return view('elearning-dosen')->with('username', $username)
                                          ->with('password', $password)
                                          ->with('matkul_moodle', $matkul_moodle);
        }
        else
        {
            return view('index');
        }
    }

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

    // =================== Mahasiswa ======================
    private function mahasiswa($username, $password)
    {
        $mahasiswa = new Mahasiswa;

        $cek_user = $mahasiswa->cek_mahasiswa($username, $password);

        // jika ada syarat tidak terpenuhi seperti service mati dan salah password
        if($cek_user['status'] == 'gagal')
        {
            return redirect('/')->with('warning', $cek_user['pesan']);
        }
        // jika terpenuhi
        else
        {
            session()->put('nama_mhs', $mahasiswa->data['nama']);
            session()->put('username', $mahasiswa->data['username']);
            session()->put('password', $mahasiswa->data['password']);

            $cek_email = $mahasiswa->cek_email_mahasiswa($username);
            // jika belum ada email
            if($cek_email['ada'] == 0)
            {
                session()->put('email', 'belum-ada');

                return redirect('registrasi-email');
            }
            // jika sudah ada email
            elseif($cek_email['ada'] == 1)
            {
                session()->put('email', $cek_email['email']);
                session()->put('password_email', $cek_email['password_email']);

                // jika ada email maka periksa udah terdaftar di moodle belum
                $cek_moodle = $mahasiswa->cek_moodle_mahasiswa($username);
                // jika belum ada di moodle, maka di daftarkan di moodle
                if($cek_moodle['ada'] == 0)
                {
                    $daftar = $mahasiswa->daftar_mahasiswa($username, $password, $cek_email['email']);
                    // jika sudah terdaftar di moodle, maka akan punya id, untuk di enrol sebahai mahasiswa
                    if(isset($daftar[0]->id))
                    {
                        $mahasiswa->assign_role_mahasiswa($daftar[0]->id, 5);

                        session()->put('moodle_id', $daftar[0]->id);
                        session()->put('status', 'mahasiswa');

                        return redirect('/');
                    }
                }
                // jika sudah ada di moodle
                elseif($cek_moodle['ada'] == 1)
                {
                    $mahasiswa->update_mahasiswa($cek_moodle['moodle_id'], $password, $cek_email['email']);

                    session()->put('moodle_id', $cek_moodle['moodle_id']);
                    session()->put('status', 'mahasiswa');

                    return redirect('/');
                }
            }
        }
    }

    // ================== Dosen ===========================
    private function dosen($username, $password)
    {
        $dosen = new Dosen;

        $cek_user = $dosen->cek_dosen($username, $password);
        if($cek_user['status'] == 'gagal')
        {
            return redirect('/')->with('warning', $cek_user['pesan']);
        }
        else
        {
            session()->put('nama_dosen', $dosen->data['nama']);
            session()->put('username', $username);
            session()->put('password', $password);

            $cek_email = $dosen->cek_email_dosen($username);
            // jika belum ada email
            if($cek_email['ada'] == 0)
            {
                session()->put('email-dosen', 'belum-ada');

                return redirect('validasi-email-dosen');
            }
            elseif($cek_email['ada'] == -1)
            {
                return redirect('/')->with('warning', 'Hubungi Operator E-learning di helpdesk-elearning@untan.ac.id untuk mendaftarkan Akun Anda');
            }
            // jika sudah ada email
            elseif($cek_email['ada'] == 1)
            {
                // jika ada email maka periksa udah terdaftar di moodle belum
                $cek_moodle = $dosen->cek_moodle_dosen($username);
                // jika belum ada di moodle, maka di daftarkan di moodle
                if($cek_moodle['ada'] == 0)
                {
                     $daftar = $dosen->daftar_dosen($username, $password, $cek_email['email']);
                     // jika sudah terdaftar di moodle, maka akan punya id, untuk di enrol sebahai dosen
                    if(isset($daftar[0]->id))
                    {
                        $dosen->assign_role_dosen($daftar[0]->id, 3);

                        session()->put('moodle_id', $daftar[0]->id);
                        session()->put('status', 'dosen');

                        return redirect('/');
                    }
                }
                // jika sudah ada di moodle
                elseif($cek_moodle['ada'] == 1)
                {
                    $dosen->update_dosen($cek_moodle['moodle_id'], $password, $cek_email['email']);

                    session()->put('moodle_id', $cek_moodle['moodle_id']);
                    session()->put('status', 'dosen');

                    return redirect('/');
                }
            }
        }
    }

    public function getVerifikasi()
    {
        session()->flush();
        return redirect('/');
    }

    public function logout()
    {
        session()->flush();
        return redirect('/');
    }
    
}

?>