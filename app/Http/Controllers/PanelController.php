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

    public function listPeriode()
    {
        $periode = DB::table('periode')->limit(5)->orderBy('id', 'DESC')->get();

        $data['periode'] = $periode;

        return view('learning-panel.periode', $data);
    }

    public function importPeriode($id)
    {
        $functionname = 'core_course_create_categories';
        $restformat = 'json';
        $serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

        $periode = DB::table('periode')->where('id', '=', $id)->first();
        $name = str_replace('-', ' ', $periode->tahun_akademik);

        $data = array();
        $item = array();
        $item['name'] = $name;
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

        DB::table('periode')->where('id', '=', $id)->update(array('moodle_periode_id'=>$return[0]->id));

        return redirect()->back();
    }

    public function listFakultas(Request $request)
    {
        $periode_id = DB::table('periode')->max('id');
        if($request->has('periode_id'))
        {
            $periode_id = $request->periode_id;
        }

        $periode = DB::table('periode')->limit(5)->orderBy('id', 'DESC')->get();
        $fakultas = DB::table('fakultas')->where('periode_id', $periode_id)->get();
        $data['fakultas'] = $fakultas;
        $data['periode'] = $periode;
        $data['periode_id'] = $periode_id;

        return view('learning-panel.fakultas', $data);
    }

    public function importFakultas($id)
    {
        $functionname = 'core_course_create_categories';
        $restformat = 'json';
        $serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

        $fakultas = DB::table('fakultas')->where('id', $id)->first();

        $parent_id = DB::table('periode')->where('id', $fakultas->periode_id)->first()->moodle_periode_id;

        $data = array();
        $item = array();
        $item['name'] = $fakultas->fakultas;
        $item['parent'] = $parent_id;
        $data[] = $item;

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

        DB::table('fakultas')->where('id', $id)->update(array('moodle_fakultas_id' => $return[0]->id));

        return redirect()->back();
    }

    public function listProdi(Request $request)
    {
        $periode_id = DB::table('periode')->max('id');
        $fakultas_id = 2;       // Default
        if($request->has('periode_id') && $request->has('fakultas_id'))
        {
            $periode_id = $request->periode_id;
            $fakultas_id = $request->fakultas_id;
        }

        $periode = DB::table('periode')->limit(5)->orderBy('id', 'DESC')->get();
        $fakultas = DB::table('fakultas')->where('periode_id', $periode_id)->get();

        $Prodi = DB::table('prodi')->where('periode_id', $periode_id)
                                ->where('fakultas_id', $fakultas_id)
                                ->get();

        foreach($Prodi as $key => $prodi)
        {
            $program = DB::table('program')->where('periode_id', $periode_id)
                                        ->where('prodi_id', $prodi->prodi_id)
                                        ->get();
            $Prodi[$key]->Program = $program;
        }       


        $data['prodi'] = $Prodi;
        $data['periode'] = $periode;
        $data['fakultas'] = $fakultas;
        $data['fakultas_id'] = $fakultas_id;
        $data['periode_id'] = $periode_id;

        return view('learning-panel.prodi', $data);
    }

    public function importProdi($id)
    {
        $functionname = 'core_course_create_categories';
        $restformat = 'json';
        $serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

        $prodi = DB::table('prodi')->where('id', $id)->first();

        $parent_id = DB::table('fakultas')->where('fakultas_id', $prodi->fakultas_id)
                                        ->where('periode_id', $prodi->periode_id)->first()->moodle_fakultas_id;

        $data = array();
        $item = array();
        $item['name'] = $prodi->prodi;
        $item['parent'] = $parent_id;
        $data[] = $item;

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

        DB::table('prodi')->where('id', $id)->update(array('moodle_prodi_id' => $return[0]->id));
        
        return redirect()->back();
    }

    public function importProgram($id)
    {
        $functionname = 'core_course_create_categories';
        $restformat = 'json';
        $serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

        $program = DB::table('program')->where('id', $id)->first();

        $parent_id = DB::table('prodi')->where('prodi_id', $program->prodi_id)
                                        ->where('periode_id', $program->periode_id)->first()->moodle_prodi_id;

        $data = array();
        $item = array();
        $item['name'] = $program->program;
        $item['parent'] = $parent_id;
        $data[] = $item;

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

        DB::table('program')->where('id', $id)->update(array('moodle_program_id' => $return[0]->id));
        
        return redirect()->back();
    }
}

?>