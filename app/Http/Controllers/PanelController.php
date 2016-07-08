<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PanelController extends Controller
{

    public $token = 'e195e15add7e9370355f0416dfcc306d';
    public $domain = 'http://e-learning.untan.ac.id/learning';

    // public $token = '4a69f57b2efc3edaa9eb7dfee032aafc';
    // public $domain = 'e-learning.untan.dev/moodle';

    public function listPeriode()
    {
        $Periode = DB::table('periode')->orderBy('id', 'DESC')->get();
        return view('panel-learning.periode')->with('Periode', $Periode);
    }

    public function listFakultas($id_periode)
    {
        $Fakultas = DB::table('fakultas')->where('id_periode', '=', $id_periode)->get();
        return view('panel-learning.fakultas')->with('Fakultas', $Fakultas)
                                             ->with('id_periode', $id_periode);
    }

    public function listProdi($id_periode, $id_fakultas)
    {
        $Prodi = DB::table('prodi')
                ->leftJoin('program', function($join) {
                    $join->on('prodi.id_prodi', '=', 'program.id_prodi')
                         ->on('prodi.id_fakultas', '=', 'program.id_fakultas')
                         ->on('prodi.id_periode', '=', 'program.id_periode');
                })
                ->where('prodi.id_periode', '=', $id_periode)
                ->where('prodi.id_fakultas', '=', $id_fakultas)
                ->get();

        return view('panel-learning.prodi')->with('Prodi', $Prodi)
                                          ->with('id_fakultas', $id_fakultas)
                                          ->with('id_periode', $id_periode);
    }


    /* ============================ Cek ======================================= */
    public function cekProdiProgram($periode)
    {
        $Fakultas = DB::table('fakultas')->where('periode_id', '=', $periode)->get();

        foreach($Fakultas as $fakultas)
        {
            $data = file_get_contents('http://203.24.50.30:9099/datasnap/rest/tservermethods1/prodi_jadwal/'.$fakultas->id_fakultas.'/'.$periode);
            $data = json_decode($data);

            foreach ($data->result[0] as $value)
            {
                /* Proses Cek Prodi */
                $count_prodi = DB::table('prodi')->where('periode_id', '=', $periode)
                                ->where('id_prodi', '=', $value->idprodi)->count();
                if($count_prodi == 0)
                {

                    DB::table('prodi')->insert(array(
                                'id_prodi' => $value->idprodi,
                                'prodi' => $value->prodi,
                                'periode_id' => $periode,
                                'fakultas_id' => $fakultas->id_fakultas));

                    echo $value->prodi.' dengan id '.$value->idprodi.'<br>';
                }

                /* Proses Cek Program */
                $count_program = DB::table('program')->where('periode_id', '=', $periode)
                                ->where('id_program', '=', $value->idprog)
                                ->where('prodi_id', '=', $value->idprodi)->count();

                if($count_program == 0)
                {

                    DB::table('program')->insert(array(
                                'id_program' => $value->idprog,
                                'program' => $value->prog,
                                'periode_id' => $periode,
                                'fakultas_id' => $fakultas->id_fakultas,
                                'prodi_id' => $value->idprodi));

                    echo $value->idprodi.' dengan '.$value->prog.'<br>';
                }
            }
        }
    }

    public function cekMatkul($id_periode)
    {
        $Prodi = DB::table('prodi')->where('periode_id', '=', $id_periode)->get();

        foreach($Prodi as $prodi)
        {
            $Program = DB::table('program')->where('periode_id', '=', $id_periode)->where('prodi_id', '=', $prodi->id_prodi)->get();

            foreach($Program as $program)
            {

                $data = file_get_contents('http://203.24.50.30:9099/datasnap/rest/tservermethods1/mk_jadwal/'.$id_periode.'/'.$prodi->id_prodi.'/'.$program->id_program);
                $data = json_decode($data);

                foreach($data->result[0] as $matkul)
                {

                    $count_matakuliah = DB::table('matakuliah')->where('periode_id', '=', $id_periode)
                                                  ->where('kode_matakuliah', '=', $matkul->kode)
                                                  ->where('kelas', '=', $matkul->kelas)
                                                  ->where('program', '=', $program->program)
                                                  ->where('prodi_id', '=', $prodi->id_prodi)
                                                  ->count();
                    if($count_matakuliah == 0)
                    {
                        DB::table('matakuliah')->insert(array(
                                    'id_jadwal' => $matkul->idjdw,
                                    'kode_matakuliah' => $matkul->kode,
                                    'matakuliah' => $matkul->mk,
                                    'kelas' => $matkul->kelas,
                                    'semester' => $matkul->smt,
                                    'sks' => $matkul->sks,
                                    'periode_id' => $id_periode,
                                    'fakultas_id' => $prodi->fakultas_id,
                                    'prodi_id' => $prodi->id_prodi,
                                    'program' => $program->program));

                        echo 'prodi '.$prodi->prodi.' punya progam '.$program->program.' matakuliah '. $matkul->mk.' kelas '.$matkul->kelas.'kode matakuliah'.$matkul->kode.'<br>';
                    }
                }
            }
        }
    }

    public function cekMatkulMhs($id_periode)
    {
        $html = file_get_contents('http://203.24.50.30:9099/Datasnap/Rest/Tservermethods1/jadwalperiode/'.$id_periode);

        $data = json_decode($html);
        foreach ($data->result[0] as $value) {
            $count = DB::table('matakuliah_mahasiswa')
                        ->where('nim', '=', $value->nim)
                        ->where('kode_matakuliah', '=', $value->kodemk)
                        ->where('prodi', '=', $value->progdi)
                        ->where('kelas', '=', $value->kelas)
                        ->where('program', '=', $value->program)
                        ->where('periode_id', '=', $id_periode)
                        ->count();

            if($count == 0)
            {
                DB::table('matakuliah_mahasiswa')->insert(array(
                    'nim' => $value->nim,
                    'nama' => $value->nama,
                    'kode_matakuliah' => $value->kodemk,
                    'matakuliah' => $value->namamk,
                    'prodi' => $value->progdi,
                    'kelas' => $value->kelas,
                    'program' => $value->program,
                    'periode_id' => $id_periode
                ));
            }
        }

        echo "sukses";
    }

    public function cekMatkulDsn($id_periode)
    {
        $html = file_get_contents('http://203.24.50.30:9099/Datasnap/Rest/Tservermethods1/jadwaldosenperiode/'.$id_periode);

        $data = json_decode($html);
        foreach($data->result[0] as $value) {
            $count = DB::table('matakuliah_dosen')
                        ->where('nip', '=', $value->nip)
                        ->where('kode_matakuliah', '=', $value->kodemk)
                        ->where('prodi', '=', $value->progdi)
                        ->where('kelas', '=', $value->kelas)
                        ->where('program', '=', $value->program)
                        ->where('periode_id', '=', $id_periode)
                        ->count();

            if($count == 0)
            {
                DB::table('matakuliah_dosen')->insert(array(
                    'nip' => $value->nip,
                    'nama' => $value->nama,
                    'gelar_depan' => $value->gelardepan,
                    'gelar_belakang' => $value->gelarbelakang,
                    'kode_matakuliah' => $value->kodemk,
                    'matakuliah' => $value->namamk,
                    'prodi' => $value->progdi,
                    'program' => $value->program,
                    'periode_id' => $id_periode
                ));
            }
        }

        echo "sukses";
    }

    /* ============================ Import ================================== */
    public function importPeriode($id_periode)
    {
        $functionname = 'core_course_create_categories';
        $restformat = 'json';
        $serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

        $periode = DB::table('periode')->where('id', '=', $id_periode)->first();

        $data = array();
        $item = array();
        $item['name'] = $periode->periode.' '.ucfirst($periode->semester);
        $data[] = $item;

        $params = array('categories' => $data);

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

        $return = json_decode($result);

        DB::table('periode')->where('id', '=', $id_periode)->update(array('moodle_periode_id'=>$return[0]->id));
    }

    public function importFakultas($id_periode)
    {
        $functionname = 'core_course_create_categories';
        $restformat = 'json';
        $serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

        $count_fakultas = DB::table('fakultas')->where('periode_id', '=', $id_periode)
                                    ->where('moodle_fakultas_id', '=', 0)->count();
        if($count_fakultas > 0)
        {
            $Fakultas = DB::table('fakultas')->where('periode_id', '=', $id_periode)
                                ->where('moodle_fakultas_id', '=', 0)
                                ->get();
            $parent_id = DB::table('periode')->where('id', '=', $id_periode)->first()->moodle_periode_id;

            $data = array();
            foreach($Fakultas as $fakultas)
            {
                $item = array();
                $item['name'] = $fakultas->fakultas;
                $item['parent'] = $parent_id;
                $data[] = $item;
            }

            //url-ify the data for the POST
            $params = array('categories' => $data);
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

            $return = json_decode($result);

            foreach($Fakultas as $key => $fakultas)
            {
                DB::table('fakultas')->where('id', '=', $fakultas->id)->update(array('moodle_fakultas_id'=>$return[$key]->id));
            }
        }
    }

    public function importProdi($id_periode)
    {
        $functionname = 'core_course_create_categories';
        $restformat = 'json';
        $serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

        $count_prodi = DB::table('prodi')->where('periode_id', '=', $id_periode)
                            ->where('moodle_prodi_id', '=', 0)->count();

        if($count_prodi > 0)
        {
            $Prodi = DB::SELECT("SELECT prodi.id, prodi.prodi,
                                        prodi.moodle_prodi_id,
                                        fakultas.moodle_fakultas_id AS parent_id
                                FROM prodi
                                LEFT JOIN fakultas ON fakultas.id_fakultas = prodi.fakultas_id
                                AND fakultas.periode_id = prodi.periode_id
                                WHERE fakultas.periode_id = '$id_periode'
                                AND prodi.moodle_prodi_id = 0");

            $data = array();
            foreach($Prodi as $prodi)
            {
                $item = array();
                $item['name'] = $prodi->prodi;
                $item['parent'] = $prodi->parent_id;
                $data[] = $item;
            }

            //url-ify the data for the POST
            $params = array('categories' => $data);
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

            $return = json_decode($result);


            foreach($Prodi as $key => $prodi)
            {
                DB::table('prodi')->where('id', '=', $prodi->id)->update(array('moodle_prodi_id'=>$return[$key]->id));
            }
        }
    }

    public function importProgram($id_periode)
    {
        $functionname = 'core_course_create_categories';
        $restformat = 'json';
        $serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

        $count_program = DB::table('program')->where('periode_id', '=', $id_periode)
                         ->where('moodle_program_id', '=', 0)->count();

        if($count_program > 0)
        {
            $Program = DB::SELECT("SELECT program.id,
                                          program.program,
                                          program.moodle_program_id,
                                          prodi.moodle_prodi_id AS parent_id FROM program
                                    LEFT JOIN prodi ON program.prodi_id = prodi.id_prodi
                                    AND program.periode_id = prodi.periode_id
                                    WHERE program.periode_id = '$id_periode'
                                    AND program.moodle_program_id = 0");
            $data = array();
            foreach($Program as $program)
            {
                $item = array();
                $item['name'] = $program->program;
                $item['parent'] = $program->parent_id;
                $data[] = $item;
            }

            //url-ify the data for the POST
            $params = array('categories' => $data);
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

            $return = json_decode($result);

            foreach($Program as $key => $program)
            {
                DB::table('program')->where('id', '=', $program->id)->update(array('moodle_program_id'=>$return[$key]->id));
            }
        }
    }
}

?>