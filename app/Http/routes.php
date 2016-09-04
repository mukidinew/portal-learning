<?php 

Route::get('/', 'PeriksaController@index');
Route::get('verifikasi', 'PeriksaController@logout');
Route::get('logout', 'PeriksaController@logout');
Route::post('verifikasi', 'PeriksaController@verifikasi');

Route::get('registrasi-email', 'EmailController@createEmail');
Route::post('registrasi-email','EmailController@insertEmail');
Route::get('validasi-email-dosen', 'EmailController@validasiEmailDosen');
Route::post('validasi-email-dosen', 'EmailController@prosesValidasiEmailDosen');

Route::get('matakuliah/detail', 'MatakuliahController@detailMatakuliah');
Route::get('matakuliah/enable', 'MatakuliahController@enableMatakuliah');
Route::get('enrol/mahasiswa', 'MatakuliahController@enrolMahasiswa');
Route::get('enrol/dosen', 'MatakuliahController@enrolDosen');

Route::group(['prefix' => 'panel-learning'], function() {

	Route::get('/', 'PanelController@listPeriode');
	Route::get('/fakultas', 'PanelController@listFakultas');
	Route::get('/prodi', 'PanelController@listProdi');

	Route::get('/import/periode/{id}', 'PanelController@importPeriode');
	Route::get('/import/fakultas/{id}', 'PanelController@importFakultas');
	Route::get('/import/prodi/{id}', 'PanelController@importProdi');
	Route::get('/import/program/{id}', 'PanelController@importProgram');

});