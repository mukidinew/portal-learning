@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="box-panel">
				<h2 class="title-panel">List Periode Akademik</h2>
				<ul class="list-panel">
					@foreach($periode as $key => $value)
					<li>
					Periode ID : {{$value->id}} / Moodle ID : {{$value->moodle_periode_id}} / 
					@if($value->moodle_periode_id == NULL)
					<a href="{{url('learningpanel/import/periode/'.$value->id)}}" class="btn btn-xs btn-primary pull-right">Import ke Moodle</a>
					@endif
					<a href="{{url('learningpanel/fakultas?periode_id='.$value->id)}}" class="btn btn-xs btn-warning pull-right">Lihat Fakultas</a>
					<p>{{$value->tahun_akademik}}</p>

					</li>
					@endforeach
				</ul>
			</div>
		</div>
	</div>
</div>

@endsection