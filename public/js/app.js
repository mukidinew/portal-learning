$(document).ready(function() {

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
			$('#form-email-dosen').submit();
		}
	});

	$('.cover').click(function() {
		$('.cover, .box-detail-matkul').hide();
		$('.list-matkul-mahasiswa').empty();
		$("#total-mahasiswa").empty();
	});
	
});

function detail_matkul_dosen(jadwal_id, periode_id)
{
	$('.cover, .box-detail-matkul').show();
	$('#alert-loading').append("Sedang Loading....");
	$.ajax({
		url: 'matakuliah/detail',
		type: 'GET',
		data: {jadwal_id: jadwal_id, periode_id: periode_id},
		success: function(data) {
			$('#alert-loading').empty();
			$("#total-mahasiswa").append("Total: " + data.length+ " Mahasiswa");
			console.log(data);
			$.each(data, function(index, value) {
				var status = '';

				if(value.moodle_mahasiswa_id == null)
				{
					status = "Belum Terdaftar di E-Learning";
				}
				else if(value.moodle_mahasiswa_enroll == null)
				{
					status = "Belum Enroll Matakuliah";
				}
				else if(value.moodle_mahasiswa_id != null && value.moodle_mahasiswa_enroll != null)
				{
					status = "Sudah Enroll Matakuliah";
				}
				$('.list-matkul-mahasiswa').append("<li>"+value.nama_mahasiswa+" ("+value.nim+")<span>"+status+"</span></li>");
			});
		}
	});
}

