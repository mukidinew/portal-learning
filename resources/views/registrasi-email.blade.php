@extends('layouts.app')

@section('content')

<section id="banner-email">
	<div class="container">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<div class="box-form-email">
					<h2>Silahkan Masukkan Data Anda</h2>
					<div class="row">
						<div class="col-md-6">
							<form action="" method="POST" class="form-email">
								@if(session('warning'))
									<div class="alert alert-danger" style="padding: 10px; margin-top: 5px;">
								        {{ session('warning') }}
								    </div>
								@endif
								<div class="form-group">
									@if ($errors->has('first_name')) <p class="blok-pesan-error">{{ $errors->first('first_name') }}</p> @endif
									<input type="text" id="first_name" name="first_name" autocomplete="off" class="form-control" placeholder="First Name" value="{{ Request::old('first_name') }}">
								</div>

								<div class="form-group" @if ($errors->has('last_name')) has-error @endif ">
									@if ($errors->has('last_name')) <p class="blok-pesan-error">{{ $errors->first('last_name') }}</p> @endif
									<input type="text" id="last_name" name="last_name" autocomplete="off" class="form-control" placeholder="Last Name" value="{{ Request::old('last_name') }}">
								</div>

								<div class="form-group">
									@if ($errors->has('email')) <p class="blok-pesan-error">{{ $errors->first('email') }}</p> @endif
									<input type="text" name="email" autocomplete="off" id="email" class="form-control" placeholder="Email" value="{{ Request::old('email') }}"> <span> @student.untan.ac.id</span>
								</div>

								<div class="form-group">
									@if ($errors->has('password')) <p class="blok-pesan-error">{{ $errors->first('password') }}</p> @endif
									<input type="password" name="password" autocomplete="off" placeholder="Password (Min. 9 karakter)" class="form-control">
								</div>

								<div class="form-group">
									@if ($errors->has('password_confirm')) <p class="blok-pesan-error">{{ $errors->first('password_confirm') }}</p> @endif
									<input type="password" name="password_confirm" autocomplete="off" placeholder="Confirm Password" class="form-control">
								</div>

								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="submit" value="Buat Email" class="btn btn-lg btn-submit-email">
							</form>
						</div>
						<div class="col-md-6">
							<div class="side-keterangan">
								<img src="{{url('images/gmail.png')}}" alt="" class="img-responsive">
							</div>
							<p class="text-keterangan">Universitas Tanjungpura mewajibkan setiap masyarakatnya untuk memiliki akun email domain <b> @untan.ac.id </b></p>
							<p>Proses pembuatan email memerlukan waktu sekitar 1-2 menit, mohon untuk sabar menunggu dan pastikan koneksi internet anda dalam kondisi stabil</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="cover"></div>

@endsection