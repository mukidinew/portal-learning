@extends('layouts.app')

@section('content')

<div class="container">
	<h2 class="page-title">Selamat Datang {{session()->get('nama_dosen')}}</h2>

	@if(session('pesan'))
		<div class="alert alert-success" style="padding: 10px; margin-top: 5px;">
	        {{ session('pesan') }}
	    </div>
	@endif

	<span class="liner"></span>

	<div class="row">
		<div class="col-md-8">
			<div class="row">
				@foreach($matkul_moodle as $key => $value)
				<div class="col-md-6">
					<div class="box-matkul">
						<div class="title-matkul">
							<h3><?php echo $value->matakuliah .'<br> Kelas '.$value->kelas; ?></h3>
						</div>
						<div class="content-matkul">
							<p>Prodi : <b>{{ $value->prodi }}</b></p>
							<p>Program : <b>{{ $value->program}}</b></p>
							<p>Kode Matakuliah : <b>{{ $value->kode_matakuliah }}</b></p>
							<p>Status Matakuliah : <b>@if($value->moodle_matakuliah_id == 0) <span style="color: #e74c3c">Belum Ada di Elearning</span> @else <span style="color: #3498db">Aktif di Elearning</span> @endif</b></p>
							<p style="margin-bottom: 10px;">Status Dosen: <b>@if($value->sudah_enrol == 0) <span style="color: #e74c3c">Belum Enroll</span> @else <span style="color: #3498db">Aktif di Elearning</span> @endif</b></p>

							<!-- Button Aktif Matakuliah -->
							@if($value->moodle_matakuliah_id == 0)
								<a href="{{ url('matakuliah/enable?kode_matakuliah='.$value->kode_matakuliah.'&id_prodi='.$value->id_prodi.'&program='.$value->program.'&kelas='.$value->kelas.'&id_periode='.$value->id_periode.'&id_jadwal='.$value->id_jadwal) }}" class="btn btn-warning">Aktikan</a>
							@endif

							<!-- Button Detail -->
							<button class="btn btn-primary" onclick='detail_matkul_dosen("{{ $value->id_jadwal }}","{{ $value->id_periode }}")'>Detail</button>

							@if($value->sudah_enrol == 0 && $value->moodle_matakuliah_id != 0)
								<a href="{{ url('enrol/dosen?id_jadwal='.$value->id_jadwal.'&id_periode='.$value->id_periode) }}" class="btn btn-success">Gabung di Matakuliah</a>
							@endif
							
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
		<div class="col-md-4">
			<form action="http://e-learning.untan.ac.id/learning/login/index.php" method="POST">
				<input type="hidden" name="username" value="{{ $username }}">
				<input type="hidden" name="password" value="{{ $password }}">
				<input type="submit" class="btn btn-primary" value="Menuju E-learning">
			</form>
			<div class="box-sidebarProcedure">
				<h2>Petunjuk Penggunaan</h2>
				<ol>
					<li>Pastikan Data Matakuliah yang terdaftar pada sistem E-learning UNTAN sama dengan mata kuliah yang ada di SIAKAD UNTAN</li>
					<li>Dosen Menentukan Matakuliah apa saja yang ingin di aktifkan di Sistem E-Learning UNTAN dengan klik tombol <b>Aktifkan</b>. Secara otomatis dosen dan mahasiswa akan terdaftar pada matakuliah yang di aktifkan.</li>
					<li>Data Mahasiswa yang mengambil matakuliah dapat dilihat pada tombol <b>detail</b> untuk dibandingkan dengan data siakad</li>
					<li>Klik Tombol <b>Menuju E-Learning</b> untuk masuk pada sistem E-Learning UNTAN.</li>
					<li>Jika mempunyai masalah dalam menggunakan sistem E-Learning UNTAN, maka silahkan kirim email pengaduan ke <b>helpdesk-elearning@untan.ac.id</b></li>
				</ol>
			</div>
			<h3 class="page-title" style="font-size:25px;">Link Terkait</h3>
			<span class="liner"></span>
			<ul class="sidebar-link">
				<li><a href="http://kuliahdaring.dikti.go.id">Kuliah Daring DIKTI</a></li>
				<li><a href="http://jurnal.untan.ac.id">Jurnal UNTAN</a></li>
				<li><a href="http://www.untan.ac.id">Website UNTAN</a></li>
				<li><a href="http://siakad.untan.ac.id">SIAKAD UNTAN</a></li>
			</ul>
		</div>
	</div>
</div>

<div class="cover"></div>
<div class="box-detail-matkul">
	<h3>Detail Matakuliah</h3>
	<span class="liner"></span>
	<p id="alert-loading"></p>
	<p id="total-mahasiswa"></p>
	<ol class="list-matkul-mahasiswa">
		{{-- <li>M. Adi Akbar (K11110016) <span>Sudah Enrol Matakuliah</span></li>
		<li>M. Adi Akbar (K11110016) <span>Belum Enrol Matakuliah</span></li>
		<li>M. Adi Akbar (K11110016) <span>Belum Terdaftar di Elearning</span></li> --}}
	</ol>
</div>

@endsection