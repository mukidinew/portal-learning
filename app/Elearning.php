<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Elearning extends Model
{
    public $token = 'e195e15add7e9370355f0416dfcc306d';
	public $domain = 'http://e-learning.untan.ac.id/learning';

	public function import_matakuliah($jadwal_id, $periode_id)
	{
		$functionname = 'core_course_create_courses';
		$restformat = 'json';
		$serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

		$matakuliah = DB::table('matakuliah')
										->select('*', 'matakuliah.id as matakuliah_id')
	  								->leftJoin('program', function($join) {
	  									$join->on('matakuliah.program_id', '=', 'program.program_id')
	  											 ->on('matakuliah.prodi_id', '=', 'program.prodi_id')
	  											 ->on('matakuliah.periode_id', '=', 'program.periode_id');
	  								})
										->where('jadwal_id', $jadwal_id)
										->where('matakuliah.periode_id', $periode_id)
										->first();

		$data = array();
		$item = array();
		$item['fullname'] = $matakuliah->matakuliah.' Kelas '.$matakuliah->kelas.' '.$matakuliah->program;
		$item['shortname'] = $matakuliah->kode_matakuliah.' '.$matakuliah->kelas.' '.$matakuliah->jadwal_id;
		$item['categoryid'] = $matakuliah->moodle_program_id;
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

		DB::table('matakuliah')->where('id', $matakuliah->matakuliah_id)->update(array(
			'moodle_matakuliah_id' => $hasil[0]->id
		));

		return $hasil;
	}

	public function enroll_dosen($jadwal_id, $periode_id, $course_id)
	{
		$functionname = 'enrol_manual_enrol_users';
		$restformat = 'json';
		$serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

		$Dosen = DB::table('matakuliah_dosen')
								->select('*', 'matakuliah_dosen.id as matakuliah_dosen_id')
								->leftJoin('dosen', 'matakuliah_dosen.nip', '=', 'dosen.nip')
								->whereNotNull('moodle_dosen_id')
								->whereNull('moodle_dosen_enroll')
								->where('jadwal_id', $jadwal_id)
								->where('periode_id', $periode_id)
								->get();

		if(count($Dosen) > 0)
		{
			$data = array();
			foreach($Dosen as $dosen)
			{
				$item = array();
				$item['roleid'] = 3;
				$item['userid'] = $dosen->moodle_dosen_id;
				$item['courseid'] = $course_id;
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

			$hasil = json_decode($result);

			foreach($Dosen as $dosen)
			{
				DB::table('matakuliah_dosen')->where('id', $dosen->matakuliah_dosen_id)
					->update(array('moodle_dosen_enroll' => 1));
			}
			return true;
		}
	}

	public function enroll_mahasiswa($jadwal_id, $periode_id, $course_id)
	{
		$functionname = 'enrol_manual_enrol_users';
		$restformat = 'json';
		$serverurl = $this->domain . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionname.'&moodlewsrestformat=' . $restformat;

		$Mahasiswa = DB::table('matakuliah_mahasiswa')
										->select('*', 'matakuliah_mahasiswa.id as matakuliah_mahasiswa_id')
										->leftJoin('mahasiswa', 'matakuliah_mahasiswa.nim', '=', 'mahasiswa.nim')
										->whereNotNull('moodle_mahasiswa_id')
										->whereNull('moodle_mahasiswa_enroll')
										->where('jadwal_id', $jadwal_id)
										->where('periode_id', $periode_id)
										->get();

		if(count($Mahasiswa) > 0)
		{
			$data = array();
			foreach($Mahasiswa as $mahasiswa)
			{
				$item = array();
				$item['roleid'] = 5;
				$item['userid'] = $mahasiswa->moodle_mahasiswa_id;
				$item['courseid'] = $course_id;
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

			$hasil = json_decode($result);

			foreach($Mahasiswa as $mahasiswa)
			{
				DB::table('matakuliah_mahasiswa')->where('id', $mahasiswa->matakuliah_mahasiswa_id)
					->update(array('moodle_mahasiswa_enroll' => 1));
			}
			return true;
		}
	}
}
