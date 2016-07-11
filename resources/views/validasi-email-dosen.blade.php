@extends('layouts.app')

@section('content')

<section id="banner-home">
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<form method="POST" class="form-login" id="form-email-dosen">
					<div class="form-group">
						<label for="">NIP</label>
						<input type="text" value="{{ $username }}" class="form-control" disabled="">
						<input type="hidden" name="username" value="{{ $username }}">
						<input type="hidden" name="password" value="{{ $password }}">
					</div>
					<div class="form-group">
						<label for="">Email</label>
						<input type="email" name="emaildosen" placeholder="Email Dosen" class="form-control">
					</div>
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="button" class="btn btn-primary" value="Validasi Dosen" id="validasi-dosen">
				</form>
			</div>
			<div class="col-md-9">
				<h1>Opps.. !!</h1>
				<div id="text-explanation">Kami gagal mendeteksi email dosen yang anda miliki. Untuk mempermudah proses selanjutnya mohon masukan email <span style="color: #FFCA0A;">@untan.ac.id</span>	. Jika anda <span style="color: #FFCA0A;"> LUPA / Hilang </span> alamat email UNTAN silahkan menghubungi operator Kepegawaian / PUSKOM Universitas Tanjungpura.</div>
			</div>
		</div>
	</div>
</section>

<section id="content-section">
	<div class="container">
		<h2 style="text-align: center;">Kontak Kami</h2>
		<p style="text-align: center;">email: helpdesk-elearning@untan.ac.id</p>
	</div>
</section>

<div class="cover"></div>

@endsection