$(document).ready(function(){

	$('#tombol_selesai').click(function(event){
		var username =  $("input[name='username']").val();
		var first_name = $('#first_name').val();
		var last_name = $('#last_name').val();
		var email = $('#email').val();
		var password = $('#password').val();
		var password2 = $('#password2').val();
		var token = $("input[name='_token']").val();

		if(first_name.length * last_name.length * email.length * password.length * password2.length == 0){
			alert('harap isi data dengan lengkap');
		}
		else if(email.length < 5) {
			alert('panjang email minimal 5 huruf');
		}
		else if (!/^[_0-9a-z.]*$/i.test(email)){
			alert('email hanya boleh menggunakan angka, huruf dan titik');
		}
		else if(password.length < 9) {
			alert('panjang password minimal 9 karakter');
		}
		else if(password2 != password) {
			alert('konfirmasi password anda salah');
		}
		else {
			$('.cover').show();
			// periksa email
			$.post('cek-email-ke-google?_token=' + token, {email:email}, function(data) {
				obj = $.parseJSON(data);
				// jika email belum ada
				if($.isEmptyObject(obj.users))
				{
					$.ajax({
						url: 'proses-registrasi-email?_token=' + token,
						type: 'GET',
						data: {nim:username, first_name:first_name, last_name:last_name, password:password, email:email},
						success: function(data) {
							if(data.status == 'berhasil')
							{
								$('.cover').hide();
								window.location.href = "http://e-learning.untan.ac.id";
								console.log(data);
							}
							else
							{
								$('.cover').hide();
								alert(data.pesan);
							}
							console.log(data);
						}
					});
				}
				else
				{
					alert("Email Anda sudah terdaftar, Coba lagi dengan Email lain");
					$('.cover').hide();
				}

				// else
				// {

					// console.log('bisa masuk email baru');
				// }
			});
		}
	});

	$('#validasi-dosen').click(function(event) {
		var email = $("input[name='emaildosen']").val();
		var username = $("input[name='username']").val();
		var password = $("input[name='password']").val();
		var token = $("input[name='_token']").val();
		var domainEmail = email.substr(-11);

		if(domainEmail != 'untan.ac.id')
		{
			alert("Mohon Menggunakan Email UNTAN");
		}
		else
		{
			$('.cover').show();
			$.ajax({
				url: 'validasi-email-dosen?_token=' + token,
				type: 'POST',
				data: {username: username, password: password, email: email},
				success: function(data) {
					if(data.status == 'berhasil')
					{
						$('.cover').hide();
						window.location.href = "http://e-learning.untan.ac.id";
						console.log(data);
					}
					else
					{
						$('.cover').hide();
						alert(data.pesan);
					}
					console.log(data);
				}
			});
		}
	})

});


function detail_matkul_dosen(id_jadwal, id_periode)
{
	$('.cover, .box-detail-matkul').show();
	$('#alert-loading').append("Sedang Loading....");
	$.ajax({
		url: 'matakuliah/detail',
		type: 'GET',
		data: {id_jadwal: id_jadwal, id_periode: id_periode},
		success: function(data) {
			$('#alert-loading').empty();
			$("#total-mahasiswa").append("Total: " + data.length+ " Mahasiswa");
			console.log(data);
			$.each(data, function(index, value) {
				// var status = 'helo';

				if(value.moodle_id == null)
				{
					var status = "Belum Terdaftar di Elearning";
				}
				else if(value.moodle_id != 0 && value.sudah_enrol == 0)
				{
					var status = "Belum Enroll Matakuliah";
				}
				else if(value.moodle_id !=0 && value.sudah_enrol != 0)
				{
					var status = "Sudah Enroll Matakuliah";
				}
				$('.list-matkul-mahasiswa').append("<li>"+value.nama+" ("+value.nim+")<span>"+status+"</span></li>");
			});
		}
	});
}

function enable_matkul(kode,prodi,program,kelas,periode_id)
{
	$('.cover').show();
	$.ajax({
		url: '/matakuliah/enable',
		type: 'GET',
		data: {kode: kode, prodi: prodi, program: program, kelas: kelas, periode_id: periode_id},
		success: function(data) {
			alert(data.pesan);
			location.reload();
			$('cover').hide();
		}
	});
}

function enrol_mahasiswa(kode,prodi,program,kelas,periode_id)
{
	$('.cover').show();
	$.ajax({
		url: '/enrol/mahasiswa',
		type: 'GET',
		data: {kode: kode, prodi: prodi, program: program, kelas: kelas, periode_id: periode_id},
		success: function(data) {
			alert(data.pesan);
			location.reload();
			$('cover').hide();
		}
	});
}

function enrol_dosen(kode,prodi,program,kelas,periode_id)
{
	$('.cover').show();
	$.ajax({
		url: '/enrol/dosen',
		type: 'GET',
		data: {kode: kode, prodi: prodi, program: program, kelas: kelas, periode_id: periode_id},
		success: function(data) {
			alert(data.pesan);
			location.reload();
			$('cover').hide();
		}
	});
}


$('.cover').click(function() {
	$('.cover, .box-detail-matkul').hide();
	$('.list-matkul-mahasiswa').empty();
	$("#total-mahasiswa").empty();
});