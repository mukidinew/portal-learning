<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatakuliahController extends Controller
{
    public function detailMatakuliah(Request $request)
    {
      $jadwal_id = $request->jadwal_id;
      $periode_id = $request->periode_id;

      $matakuliah_mahasiswa = DB::table('matakuliah_mahasiswa')
                                ->leftJoin('mahasiswa', 'matakuliah_mahasiswa.nim', '=', 'mahasiswa.nim')
                                ->where('jadwal_id', $jadwal_id)
                                ->where('periode_id', $periode_id)
                                ->get();

      return $matakuliah_mahasiswa;
    }

    public function enableMatakuliah(Request $request)
    {
      $jadwal_id = $request->jadwal_id;
      $periode_id = $request->periode_id;

      $elearning = new \App\Elearning;

      $enable_matakuliah = $elearning->import_matakuliah($jadwal_id, $periode_id);

      if(isset($enable_matakuliah[0]->id))
      {
        $elearning->enroll_dosen($jadwal_id, $periode_id, $enable_matakuliah[0]->id);
        $elearning->enroll_mahasiswa($jadwal_id, $periode_id, $enable_matakuliah[0]->id);
      }

      return back()->with('pesan', 'Matakuliah Sudah Aktif Di E-learning');
    }

    public function enrolMahasiswa(Request $request)
    {
      $jadwal_id = $request->jadwal_id;
      $periode_id = $request->periode_id;
      $course_id = $request->course_id;

      $elearning = new \App\Elearning;

      $elearning->enroll_mahasiswa($jadwal_id, $periode_id, $course_id);

      return back()->with('pesan', 'Berhasil join dimatakuliah');
    }

    public function enrolDosen(Request $request)
    {
      $jadwal_id = $request->jadwal_id;
      $periode_id = $request->periode_id;
      $course_id = $request->course_id;

      $elearning = new \App\Elearning;

      $elearning->enroll_dosen($jadwal_id, $periode_id, $course_id);

      return back()->with('pesan', 'Berhasil join dimatakuliah');
    }
}
