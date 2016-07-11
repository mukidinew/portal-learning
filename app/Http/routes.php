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
