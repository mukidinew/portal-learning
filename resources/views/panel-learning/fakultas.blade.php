<a href="">Cek Fakultas</a> <br>

<a href="{{ url('panel-learning/import/fakultas/'.$id_periode) }}">Import semua Fakultas</a> <br>
<a href="{{ url('panel-learning/import/prodi/'.$id_periode) }}">Import semua Prodi</a> <br>
<a href="{{ url('panel-learning/import/program/'.$id_periode) }}">Import semua Program</a>
<ul>
	@foreach($Fakultas as $fakultas)
	<li>
		<a href="{{ URL::to('panel-learning/'.$id_periode.'/'.$fakultas->id_fakultas) }}" style="display: inline-block; width: 200px;">
			{{ $fakultas->fakultas }}
		</a>
		<span>Moodle id: {{ $fakultas->moodle_fakultas_id }}</span>
	</li>
	@endforeach
</ul>
