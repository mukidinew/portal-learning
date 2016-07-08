<?php

namespace App\Http\Controllers;

use App\ElearningMatakuliah;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatakuliahController extends Controller
{
    public function detailMatakuliah(Request $request)
    {
      $id_jadwal = $request->id_jadwal;
      $id_periode = $request->id_periode;

      $matkul_mahasiswa = DB::table('matakuliah_mahasiswa')
                              ->leftJoin('ref_mahasiswa', function($join) {
                                  $join->on('matakuliah_mahasiswa.nim_mahasiswa', '=', 'ref_mahasiswa.nim');
                              })
                              ->where('matakuliah_mahasiswa.id_jadwal', '=', $id_jadwal)
                              ->where('matakuliah_mahasiswa.id_periode', '=', $id_periode)
                              ->get();

      return $matkul_mahasiswa;
   }

   public function enableMatakuliah(Request $request)
   {
      $kode_matakuliah = $request->kode_matakuliah;
      $id_prodi = $request->id_prodi;
      $program = $request->program;
      $kelas = $request->kelas;
      $id_periode = $request->id_periode;
      $id_jadwal = $request->id_jadwal;

      $learning = new ElearningMatakuliah;
      $enable_matkul = $learning->import_matkul($kode_matakuliah, $id_prodi, $program, $kelas, $id_periode);

      if(isset($enable_matkul[0]->id))
      {
        $learning->enrol_mahasiswa($id_jadwal, $id_periode);
        $learning->enrol_dosen($id_jadwal, $id_periode);
      }

      return back()->with('pesan', 'Matakuliah Sudah Aktif Di E-learning');
   }

   public function enrolMahasiswa(Request $request)
    {
        $id_jadwal = $request->id_jadwal;
        $id_periode = $request->id_periode;

        $learning = new ElearningMatakuliah;
        $learning->enrol_mahasiswa($id_jadwal, $id_periode);

        return back()->with('pesan', 'Terima Kasih, Anda Sudah Join di Matakuliah');
    }

    public function enrolDosen(Request $request)
    {
        $id_jadwal = $request->id_jadwal;
        $id_periode = $request->id_periode;


        $learning = new ElearningMatakuliah;
        $learning->enrol_dosen($id_jadwal, $id_periode);

        return back()->with('pesan', 'Bapak/Ibu Sudah Join di Matakuliah');
    }
}
