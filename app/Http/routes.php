<?php


Route::get('/', 'PeriksaController@index');
Route::post('verifikasi', 'PeriksaController@verifikasi');
Route::get('verifikasi', 'PeriksaController@getVerifikasi');

Route::get('matakuliah/detail', 'MatakuliahController@detailMatakuliah');
Route::get('matakuliah/enable', 'MatakuliahController@enableMatakuliah');
Route::get('enrol/mahasiswa', 'MatakuliahController@enrolMahasiswa');
Route::get('enrol/dosen', 'MatakuliahController@enrolDosen');

Route::get('registrasi-email','EmailController@createEmail');
Route::post('registrasi-email', 'EmailController@insertEmail');


Route::get('proses-registrasi-email','EmailController@insertEmail');
Route::post('cek-email-ke-google', 'EmailController@cekEmailGoogle');
Route::get('validasi-email-dosen', 'EmailController@validasiEmailDosen');
Route::post('validasi-email-dosen', 'EmailController@prosesValidasiEmailDosen');

Route::get('logout', 'PeriksaController@logout');


// ======================== Learning Panel ==============================
Route::get('panel-learning', 'PanelController@listPeriode');
Route::get('panel-learning/{id_periode}', 'PanelController@listFakultas');
Route::get('panel-learning/{id_periode}/{id_fakultas}', 'PanelController@listProdi');
// Route::get('panel-learning/{periode}/{fakultas}/{prodi}', 'PanelController@listMatkul');

Route::get('panel-learning/cek/prodi-program/{periode}', 'PanelController@cekProdiProgram');
Route::get('panel-learning/cek/matakuliah/{periode}', 'PanelController@cekMatkul');
Route::get('panel-learning/cek/matakuliah-mahasiswa/{periode}', 'PanelController@cekMatkulMhs');
Route::get('panel-learning/cek/matakuliah-dosen/{periode}', 'PanelController@cekMatkulDsn');

Route::get('panel-learning/import/periode/{periode}', 'PanelController@importPeriode');
Route::get('panel-learning/import/fakultas/{periode}', 'PanelController@importFakultas');
Route::get('panel-learning/import/prodi/{periode}', 'PanelController@importProdi');
Route::get('panel-learning/import/program/{periode}', 'PanelController@importProgram');