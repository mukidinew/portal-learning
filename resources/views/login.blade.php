@extends('layouts.app')

@section('content')

<section id="banner-home">
	<div class="container">
		<div class="row">
			<div class="col-md-9">
				<h1>Selamat Datang di <br />Portal E-Learning Universitas Tanjungpura</h1>
				<div id="text-explanation">Media Pembelajaran berbasis Online untuk Civitas Akademika</div>
				@if (session('warning'))
				    <div class="alert alert-danger" style="padding: 10px; margin-top: 5px;">
				        {{ session('warning') }}
				    </div>
				@endif
			</div>
			<div class="col-md-3">
				<form method="POST" action="verifikasi" class="form-login">
					<div class="form-group">
						<label for="">Username / Nim</label>
						<input type="text" name="username" placeholder="Username / Nim" class="form-control">
					</div>
					<div class="form-group">
						<label for="">Password</label>
						<input type="password" name="password" placeholder="Password" class="form-control">
					</div>
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="submit" class="btn btn-primary" value="Login">
				</form>

			</div>
		</div>

	</div>
</section>
<section id="content-section">
	<div class="container">
		<h2 style="text-align: center; margin-bottom: 45px;">Penjelasan E-learning</h2>
		<div class="row">
			<div class="col-md-3 box-procedure">
				<span class="glyphicon glyphicon-new-window" aria-hidden="true"></span>
				<h4>Masuk ke Portal</h4>
				<p>Masuk dengan menggunakan NIM dan Password Siakad</p>
			</div>
			<div class="col-md-3 box-procedure">
				<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
				<h4>Verifikasi Data</h4>
				<p>Sistem akan mengecek data email serta matakuliah yang di ambil sesuai dengan data SIAKAD UNTAN</p>
			</div>
			<div class="col-md-3 box-procedure">
				<span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span>
				<h4>Aktifkan Matakuliah</h4>
				<p>Dosen akan menentukan matakuliah yang akan di aktifkan di Elearning</p>
			</div>
			<div class="col-md-3 box-procedure">
				<span class="glyphicon glyphicon-book" aria-hidden="true"></span>
				<h4>Mulai Belajar</h4>
				<p>Dosen dan Mahasiswa melangsungkan kuliah berbasis online menggunakan E-learning UNTAN</p>
			</div>
		</div>
	</div>

</section>


@stop