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

				if(value.moodle_id == null || value.moodle_id == "")
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
				$('.list-matkul-mahasiswa').append("<li>"+value.nama_mahasiswa+" ("+value.nim_mahasiswa+")<span>"+status+"</span></li>");
			});
		}
	});
}

