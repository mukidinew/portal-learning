@extends('layouts.app')

@section('content')

<div class="container">
	<h2 class="page-title">Selamat Datang {{ session()->get('nama_mhs') }}</h2>
    <p style="margin-bottom: 0;"><b>Email:</b> {{ $email }}</p>
    <p><b>Password:</b> {{ $password_email }}</p>
    <p style="font-size: 16px; width: 80%;">Mohon untuk aktif menggunakan Email UNTAN, karena jika di biarkan <b>bekapok tak pernah di buka lebih dari 1 bulan</b> akan di non-aktifkan oleh ADMIN</p>

    @if(session('pesan'))
		<div class="alert alert-warning" style="padding: 10px; margin-top: 5px;">
	        {{ session('pesan') }}
	    </div>
	@endif

    <span class="liner"></span>

	<div class="row">
		<div class="col-md-8">
			<div class="row">
				@foreach($matkul_moodle as $key => $value)
				<div class="col-md-6">
					<div class="box-matkul" style="height: 350px;">
						<div class="title-matkul">
							<h3><?php echo $value->matakuliah .'<br> Kelas '.$value->kelas; ?></h3>
						</div>
						<div class="content-matkul">
							<p>Prodi : <b>{{ $value->prodi }}</b></p>
							<p>Program : <b>{{ $value->program}}</b></p>
							<p>Kode Matakuliah : <b>{{ $value->kode_matakuliah }}</b></p>
							<p>Status Matakuliah : <b> 
								@if($value->moodle_matakuliah_id == 0) 
									<span style="color: #e74c3c"> Belum Ada di Elearning </span> 
								@else 
									<span style="color: #3498db">Aktif di Elearning</span>
								@endif</b></p>
							<p>Status Mahasiswa : <b>
								@if($value->sudah_enrol == 0)
									<span style="color: #e74c3c"> Belum Terdaftar di Matakuliah</span> 
								@else
									<span style="color: #3498db"> Sudah Terdaftar di Matakuliah</span> 
								@endif
							</b></p>
							@if($value->moodle_matakuliah_id != 0)
								<a href="{{ url('enrol/mahasiswa?id_jadwal='.$value->id_jadwal.'&id_periode='.$value->id_periode) }}" class="btn btn-success" style="margin-top: 15px;">Gabung di Matakuliah</a>
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
					<li>Pastikan Data Matakuliah yang terdaftar pada sistem E-learning UNTAN sama dengan mata kuliah yang di ambil dengan SIAKAD UNTAN</li>
					<li>Jika Dosen sudah mengaktifkan mata kuliah pada sistem E-learning maka <b>anda otomatis akan terdaftar</b> sebagai peserta pada matakuliah tersebut</li>
					<li>Cek Status Mahasiswa apakah <b>Sudah Terdaftar</b> atau <b>Belum Terdaftar</b> pada Matakuliah. Jika belum, maka silahkan klik tombol <b>Gabung di Mata kuliah</b>.</li>
					<li>Klik Tombol <b>Menuju E-Learning</b> untuk masuk pada sistem E-Learning UNTAN.</li>
					<li>Pastikan Data <b>Email Student</b> Anda masih aktif dengan mengeceknya minimal 1 bulan sekali dan sesuai dengan data profil anda di sistem E-learning.</li>
					<li>Jika E-mail student anda <b>tidak aktif</b> atau <b>salah password</b>, maka silahkan kirim email pengaduan ke <b>helpdesk-elearning@untan.ac.id</b></li>
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

@endsection