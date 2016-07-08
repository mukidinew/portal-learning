<a href="">Cek Periode</a>
<ul>
	@foreach($Periode as $periode)
	<li>
		<a href="{{ URL::to('panel-learning/'.$periode->id_periode) }}">
			{{ $periode->periode.' '.$periode->semester.' ('.$periode->id_periode.')' }}
		</a>
		<span style="margin-left: 20px;">Moodle id: {{ $periode->moodle_periode_id }}</span>
		<a style="margin-left: 20px;" href="{{ url('import-periode/'.$periode->id) }}">import periode</a>
	</li>
	@endforeach
</ul>