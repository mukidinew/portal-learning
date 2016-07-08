<?php 

Route::get('/', 'PeriksaController@index');
Route::get('verifikasi', 'PeriksaController@logout');
Route::get('logout', 'PeriksaController@logout');
Route::post('verifikasi', 'PeriksaController@verifikasi');
