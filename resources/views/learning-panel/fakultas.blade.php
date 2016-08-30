@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="box-panel">
				<h2 class="title-panel">List Fakultas</h2>
				<form action="" method="get" role="form" class="form-inline">
					<div class="form-group">
						<select name="periode_id" id="" class="form-control form-panel">
							@foreach($periode as $key => $value)
							<option value="{{$value->id}}" @if($value->id == $periode_id) selected @endif>{{$value->tahun_akademik}}</option>
							@endforeach
						</select>
					</div>

					<button type="submit" class="btn btn-primary">Submit</button>
				</form>
				<ul class="list-panel" style="margin-top: 20px;">
					@foreach($fakultas as $key => $value)
					<li>{{$value->fakultas}} / Moodle ID : {{$value->moodle_fakultas_id}}
					@if($value->moodle_fakultas_id == NULL)
					<a href="{{url('learningpanel/import/fakultas/'.$value->id)}}" class="btn btn-xs btn-primary pull-right">Import ke Moodle</a>
					@endif
					<a href="{{url('learningpanel/prodi?periode_id='.$periode_id.'&fakultas_id='.$value->fakultas_id)}}" class="btn btn-xs btn-warning pull-right">Lihat Prodi</a>
					</li>
					@endforeach
				</ul>
			</div>
		</div>
	</div>
</div>

@endsection