<a href="{{ url('panel-learning/'.$id_periode.'/'.$id_fakultas.'/cek/prodi-program') }}">Cek Prodi dan Program</a> <br>
<a href="{{ url('panel-learning/'.$id_periode.'/'.$id_fakultas.'/cek/matakuliah') }}">Cek Matakuliah</a> <br>
<a href="{{ url('panel-learning/'.$id_periode.'/'.$id_fakultas.'/cek/matakuliah-mahasiswa') }}">Cek Matakuliah Mahasiswa</a> <br>
<a href="{{ url('panel-learning/'.$id_periode.'/'.$id_fakultas.'/cek/matakuliah-dosen') }}">Cek Matakuliah Dosen</a> <br>


<ol>
	@foreach($Prodi as $prodi)
	<li>
		<a href="{{ URL::to('panel-learning/'.$id_periode.'/'.$id_fakultas.'/'.$prodi->id_prodi) }}" style="display: inline-block; width: 500px;">
			{{ $prodi->prodi .' progam '.$prodi->program}}
		</a>
		<span>Moodle prodi id: {{ $prodi->moodle_prodi_id }}</span>
		<span style="">Moodle program id: {{ $prodi->moodle_program_id }}</span>
	</li>
	@endforeach
</ol>
