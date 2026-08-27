var tabla;

function init(){
    $("#sector_form").on("submit", function(e){
        guardaryeditar(e);
    });
}

function guardaryeditar(e){
    e.preventDefault();
    var formData = new FormData($("#sector_form")[0]);
    $.ajax({
        url: "../../controller/sector.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            if(datos == "1"){
                $('#sector_form')[0].reset();
                $("#modalmantenimiento").modal('hide');
                $('#sector_data').DataTable().ajax.reload();
                swal("Éxito!", "Sector registrado correctamente", "success");
            } else if(datos == "2"){
                $('#sector_form')[0].reset();
                $("#modalmantenimiento").modal('hide');
                $('#sector_data').DataTable().ajax.reload();
                swal("Éxito!", "Sector actualizado correctamente", "success");
            }
        }
    });
}

$(document).ready(function(){
    tabla = $('#sector_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: "Bfrtip",
        buttons: ["copyHtml5","excelHtml5","csvHtml5","pdfHtml5"],
        "ajax": {
            url: "../../controller/sector.php?op=listar",
            type: "post",
            dataType: "json",
            error: function(e){ console.log(e.responseText); }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "autoWidth": false,
        "language": {
            "sProcessing": "Procesando...",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible",
            "sInfo": "Mostrando _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 registros",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero","sLast":"Último","sNext":"Siguiente","sPrevious":"Anterior"
            }
        }
    }).DataTable();
});

function editar(sec_id){
    $.post("../../controller/sector.php?op=mostrar",{sec_id:sec_id},function(data){
        data = JSON.parse(data);
        $("#sec_id").val(data.sec_id);
        $("#sec_nom").val(data.sec_nom);
        $("#sec_descr").val(data.sec_descr);
        $("#mdltitulo").html("Editar Sector");
        $("#modalmantenimiento").modal("show");
        $("#es_soporte").prop("checked", data.es_soporte == 1);
$("#sec_correo_ticket").val(data.sec_correo_ticket);
    });
}

function eliminar(sec_id){
    swal({
        title: "¿Está seguro?",
        text: "Esta acción no se puede deshacer",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si, eliminar",
        cancelButtonText: "No",
        closeOnConfirm: false
    }, function(isConfirm){
        if(isConfirm){
            $.post("../../controller/sector.php?op=eliminar",{sec_id:sec_id},function(data){});
            $('#sector_data').DataTable().ajax.reload();
            swal("Eliminado!","El sector fue eliminado","success");
        }
    });
}

$(document).on("click","#btnnuevo",function(){
    $("#sec_id").val("");
    $("#sector_form")[0].reset();
    $("#mdltitulo").html("Nuevo Sector");
    $("#modalmantenimiento").modal("show");
});

init();