<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class ElearningMatakuliah extends Model
{
	public $token = 'e195e15add7e9370355f0416dfcc306d';
	public $domain = 'http://e-learning.untan.ac.id/learning';

	// public $token = '4a69f57b2efc3edaa9eb7dfee032aafc';
	// public $domain = 'e-learning.untan.dev/moodle';

	public function import_matkul($kode_matakuliah, $id_prodi, $program, $kelas, $id_periode)
	{
		$functionname = 'core_course_create_courses';
		$restformat = 'json';
		$serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

		$matkul = DB::table('matakuliah')
					->leftJoin('program', function($join) {
						$join->on('matakuliah.program', '=', 'program.program')
							 ->on('matakuliah.id_prodi', '=', 'program.id_prodi')
							 ->on('matakuliah.id_periode', '=', 'program.id_periode');
					})
					->where('matakuliah.kode_matakuliah', '=', $kode_matakuliah)
					->where('matakuliah.id_prodi', '=', $id_prodi)
					->where('matakuliah.program', '=', $program)
					->where('matakuliah.kelas', '=', $kelas)
					->where('matakuliah.id_periode', '=', $id_periode)
					->get();

		
		$data = array();
		$item = array();
		$item['fullname'] = $matkul[0]->matakuliah.' '.$matkul[0]->program.' Kelas '.$matkul[0]->kelas;
		$item['shortname'] = $matkul[0]->kode_matakuliah.' '.$matkul[0]->program.' '.$matkul[0]->kelas.' '.$id_periode;
		$item['categoryid'] = $matkul[0]->moodle_program_id;
		$item['format'] = 'topics';
		$data[] = $item;

		//url-ify the data for the POST
		$params = array('courses' => $data);
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

		$hasil = json_decode($result);

		DB::table('matakuliah')->where('id_jadwal', '=', $matkul[0]->id_jadwal)
								->update(array('moodle_matakuliah_id'=>$hasil[0]->id));

		return $hasil;
	}


	public function enrol_mahasiswa($id_jadwal, $id_periode)
	{
		$functionname = 'enrol_manual_enrol_users';
		$restformat = 'json';
		$serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

		$Mahasiswa = DB::select("SELECT
									mm.id, mm.nim_mahasiswa, mm.nama_mahasiswa, mm.sudah_enrol,
									ref_mhs.moodle_id, m.moodle_matakuliah_id
								FROM matakuliah_mahasiswa AS mm
								LEFT JOIN matakuliah AS m
								ON mm.id_jadwal = m.id_jadwal
								AND mm.id_periode = m.id_periode
								LEFT JOIN ref_mahasiswa AS ref_mhs ON mm.nim_mahasiswa = ref_mhs.nim
								WHERE mm.id_jadwal = '$id_jadwal'
								AND mm.id_periode = '$id_periode'
								AND ref_mhs.moodle_id IS NOT NULL");

		$data = array();
		foreach($Mahasiswa as $mahasiswa)
		{
			$item = array();
			$item['roleid'] = 5;
			$item['userid'] = $mahasiswa->moodle_id;
			$item['courseid'] = $mahasiswa->moodle_matakuliah_id;
			$data[] = $item;
		}

		$params = array('enrolments' => $data);

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

		foreach($Mahasiswa as $mahasiswa)
		{
			DB::table('matakuliah_mahasiswa')->where('id', '=', $mahasiswa->id)
								->update(array('sudah_enrol'=>1));
		}
	}

	public function enrol_dosen($id_jadwal, $id_periode)
	{
		$functionname = 'enrol_manual_enrol_users';
		$restformat = 'json';
		$serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

		$Dosen = DB::select("SELECT
							 md.id, md.nip, md.nama, md.sudah_enrol,
							 ref_dsn.moodle_id, m.moodle_matakuliah_id
							 FROM matakuliah_dosen AS md
							 LEFT JOIN matakuliah AS m
							 ON md.id_jadwal = m.id_jadwal
							 AND md.id_periode = m.id_periode
							 LEFT JOIN ref_dosen AS ref_dsn ON md.nip = ref_dsn.nip
							 WHERE md.id_jadwal = '$id_jadwal'
							 AND md.id_periode = '$id_periode'
							 AND ref_dsn.moodle_id IS NOT NULL");

		$data = array();
		foreach($Dosen as $dosen)
		{
			$item = array();
			$item['roleid'] = 3;
			$item['userid'] = $dosen->moodle_id;
			$item['courseid'] = $dosen->moodle_matakuliah_id;
			$data[] = $item;
		}

		$params = array('enrolments' => $data);

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

		foreach($Dosen as $dosen)
		{
			DB::table('matakuliah_dosen')->where('id', '=', $dosen->id)
								->update(array('sudah_enrol'=>1));
		}
	}
}

?>