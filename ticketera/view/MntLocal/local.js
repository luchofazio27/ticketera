var tabla;

function init(){
    $("#local_form").on("submit",function(e){
        guardaryeditar(e);
    });
}

$(document).ready(function(){
    tabla = $('#local_data').DataTable({
    "ajax": {
        url: "../../controller/local.php?op=listar",
        type: "get",
        dataType: "json"
    },
    "bDestroy": true,
    "bLengthChange": false, // 🔹 Oculta el "Mostrar X entradas"
    "language": {
        "sProcessing":     "Procesando...",
        "sLengthMenu":     "Mostrar _MENU_ registros",
        "sZeroRecords":    "No se encontraron resultados",
        "sEmptyTable":     "Ningún dato disponible en esta tabla",
        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
        "sSearch":         "Buscar:", // 🔹 Traducción al español
        "oPaginate": {
            "sFirst":    "Primero",
            "sLast":     "Último",
            "sNext":     "Siguiente",
            "sPrevious": "Anterior"
        }
    }
});


    $("#btnnuevo").click(function(){
        $("#loc_id").val("");
        $("#local_form")[0].reset();
        $("#modalmantenimiento").modal("show");
    });
});

function guardaryeditar(e){
    e.preventDefault();
    var formData = new FormData($("#local_form")[0]);
    $.ajax({
        url: "../../controller/local.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(){
            $("#modalmantenimiento").modal("hide");
            $('#local_data').DataTable().ajax.reload();
            swal("Éxito!", "Local registrado correctamente", "success");
        }
    });
}

function editar(loc_id){
    $.post("../../controller/local.php?op=mostrar",{loc_id:loc_id},function(data){
        data = JSON.parse(data);
        $("#loc_id").val(data[0].loc_id);
        $("#loc_nom").val(data[0].loc_nom);
        $("#modalmantenimiento").modal("show");
    });
}

function eliminar(loc_id){
    if(confirm("¿Desea eliminar este local?")){
        $.post("../../controller/local.php?op=eliminar",{loc_id:loc_id},function(){
            $('#local_data').DataTable().ajax.reload();
            swal("Eliminado!","El local fue eliminado","success");
        });
    }
}

function eliminar(loc_id){
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
            $.post("../../controller/local.php?op=eliminar",{loc_id:loc_id},function(){
            $('#local_data').DataTable().ajax.reload();
            swal("Eliminado!","El local fue eliminado","success");
            });
        }
    });
}

init();
