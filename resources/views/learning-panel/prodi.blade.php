@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="box-panel">
				<h2 class="title-panel">List Prodi</h2>
				<form action="" method="get" role="form" class="form-inline">
					<div class="form-group">
						<select name="periode_id" id="" class="form-control form-panel">
							@foreach($periode as $key => $value)
							<option value="{{$value->id}}" @if($value->id == $periode_id) selected @endif>{{$value->tahun_akademik}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<select name="fakultas_id" id="" class="form-control">
							@foreach($fakultas as $key => $value)
							<option value="{{$value->fakultas_id}}" @if($value->fakultas_id == $fakultas_id) selected @endif>{{$value->fakultas}}</option>
							@endforeach
						</select>
					</div>

					<button type="submit" class="btn btn-primary">Submit</button>
				</form>
				<h4 class="title-panel" style="margin-top: 20px;">Jumlah Prodi : {{count($prodi)}}</h4>
				<ul class="list-panel" style="margin-top: 20px;">
					@foreach($prodi as $key => $value)
					<li>{{$value->prodi}} / Moodle ID: {{$value->moodle_prodi_id}}
					@if($value->moodle_prodi_id == NULL)
					<a href="{{url('learningpanel/import/prodi/'.$value->id)}}" class="btn btn-xs btn-primary pull-right">Import ke Moodle</a>
					@endif
					<ul class="list-panel" style="margin-top: 10px;">
						@foreach($value->Program as $program)
						<li>{{$program->program}} / Moodle ID: {{$program->moodle_program_id}}
						@if($program->moodle_program_id == NULL && $value->moodle_prodi_id != NULL)
						<a href="{{url('learningpanel/import/program/'.$program->id)}}" class="btn btn-xs btn-primary pull-right">Import ke Moodle</a>
						@endif
						</li>
						@endforeach
					</ul>
					@endforeach
				</ul>
			</div>
		</div>
	</div>
</div>

@endsection