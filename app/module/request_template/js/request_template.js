controller = 'request_template/ex/request_template/';
var modale = document.getElementById("myModale");

$(document).ready(function(){
    $("#data_request_template").load(ctr_kfg_bayar+'data_request_template?page=1');
});

var modal_tambah_request_template = document.getElementById("modal_tambah_request_template");
var modal_edit_request_template = document.getElementById("modal_edit_request_template");

var span_modal_edit_request_template = document.getElementsByClassName("close")[0];
var span_edit_request_template = document.getElementsByClassName("close")[1];

// span_tambah_request_template.onclick = function() {
//     modal_tambah_request_template.style.display = "none";
// }
// span_edit_request_template.onclick = function() {
//     modal_edit_request_template.style.display = "none";
// }

function tampil_modal_tambah_request_template() {
    $("#modal_kelas").modal('show');
}


ctr_kfg_bayar = 'request_template/ex/request_template/';


function btn_simpan_request_template() {
    var pesan = $('#pesan').val();

    if (pesan == '') {
        alert('Pesan tidak boleh kosong!');
    } else {
        $.ajax({
            url : ctr_kfg_bayar+'simpan_request_template',
            type : "POST",
            data : {
                pesan: pesan
            },
            success: function(response) {
                alert(response);
                var pesan = $('#pesan').val('');
                $("#data_request_template").load(ctr_kfg_bayar+'data_request_template/?page=1');
            }
        });
    }
}

function tampil_modal_edit_request_template(ambilin) {
    var ambil = localStorage.setItem("ambilin", ambilin);
    var table = document.getElementById('tabsesur');
    var cells = table.getElementsByTagName('tr');

    modal_edit_request_template.style.display = "block";

    for (var i = 0, len = cells.length; i < len; i++){
        cells[i].onclick = function() {
            document.getElementById('pesan_edit').value = this.cells[2].innerText;
            document.getElementById('status_template_edit').value = this.cells[3].innerText;
            document.getElementById('nama_template_edit').value = this.cells[5].innerText;
            document.getElementById('client_id_edit').value = this.cells[6].innerText;
            document.getElementById('project_id_edit').value = this.cells[7].innerText;
            document.getElementById('username_edit').value = this.cells[8].innerText;
            document.getElementById('password_edit').value = this.cells[9].innerText;
        }
    }
    $("#modal_edit_request_template").modal('show');


}
function btn_edit_request_template() {
    var id = localStorage.getItem("ambilin");
    // alert(id);
    var pesan = $('#pesan_edit').val();
    var status = $('#status_template_edit').val();
    var nama_template = $('#nama_template_edit').val();
    var client_id = $('#client_id_edit').val();
    var project_id = $('#project_id_edit').val();
    var username = $('#username_edit').val();
    var password = $('#password_edit').val();

    if (pesan == '') {
        alert('Pesan tidak boleh kosong!');
    } else {
        $.ajax({
            url : ctr_kfg_bayar+'edit_request_template',
            type : "POST",
            data : {
                id: id,
                status: status,
                pesan: pesan,
                nama_template: nama_template,
                client_id: client_id,
                project_id: project_id,
                username: username,
                password: password
            },
            success: function(response){
                alert(response);
                $('#id_edit').val('');
                $('#pesan_edit').val('');
                $("#data_request_template").load(ctr_kfg_bayar+'data_request_template/?page=1');
                $("#modal_edit_request_template").modal('hide');
            }
        });
    }
    // $("#modal_edit_request_template").modal('show');
}


