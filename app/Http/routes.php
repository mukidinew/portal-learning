<?php 

Route::get('/', 'PeriksaController@index');
Route::get('verifikasi', 'PeriksaController@logout');
Route::get('logout', 'PeriksaController@logout');
Route::post('verifikasi', 'PeriksaController@verifikasi');

Route::get('proses-registrasi-email','EmailController@insertEmail');
Route::get('validasi-email-dosen', 'EmailController@validasiEmailDosen');
Route::post('validasi-email-dosen', 'EmailController@prosesValidasiEmailDosen');

Route::get('matakuliah/detail', 'MatakuliahController@detailMatakuliah');
Route::get('matakuliah/enable', 'MatakuliahController@enableMatakuliah');
Route::get('enrol/mahasiswa', 'MatakuliahController@enrolMahasiswa');
Route::get('enrol/dosen', 'MatakuliahController@enrolDosen');

Route::get('create-matkul-tambahan', function() {

	$jml = DB::table('email_dosen')->count();

	for($i = 1; $i <= $jml; $i++)
	{
		DB::table('matakuliah')->insert(array(
			'id_periode' => 341,
			'id_fakultas' => 0,
			'id_prodi' => 0,
			'id_jadwal' => $i,
			'kode_matakuliah' => 'TryMoodle-'.$i,
			'matakuliah' => 'Latihan '.$i,
			'kelas' => 'A',
			'semester' => 1,
			'sks' => 3,
			'prodi' => 'Prodi Tambahan',
			'program' => 'Pelatihan E-learning'
		));	
	}

});

Route::get('create-matkul-tambahan-dosen', function() {

	// $jml = DB::table('email_dosen')->count();
	$Dosen = DB::table('email_dosen')->get();

	foreach($Dosen as $key => $dosen)
	{
		DB::table('matakuliah_dosen')->insert(array() 
			'id_periode' => 341,
			'id_jadwal' => $key+1,
			'nip' => $dosen->nip,
			'nama' => $dosen->nama,
			'kode_matakuliah' => 'TryMoodle-'.$key+1,
			'matakuliah' => 'Latihan '.$key+1,
			'prodi' => 'Prodi Tambahan',
			'kelas' => 'A',
			'program' => 'Pelatihan E-learning',
		));
	}

});