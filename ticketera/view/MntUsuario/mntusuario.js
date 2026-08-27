var tabla;

function init() {
  $("#usuario_form").on("submit", function(e){
    guardaryeditar(e);
    $("#usu_id").val("");
  });
}

function guardaryeditar(e){
    e.preventDefault();
	var formData = new FormData($("#usuario_form")[0]);
    $.ajax({
        url: "../../controller/usuario.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){    
            console.log(datos);
            if(datos == "1"){
                $('#usuario_form')[0].reset();
                /* TODO:Ocultar Modal */
                $("#modalmantenimiento").modal('hide');
                $('#usuario_data').DataTable().ajax.reload();

                /* TODO:Mensaje de Confirmacion */
                swal({
                    title: "HelpDesk!",
                    text: "Registrado Correctamente.",
                    type: "success",
                    confirmButtonClass: "btn-success"
                });
            }else if(datos == "2"){
                $('#usuario_form')[0].reset();
                /* TODO:Ocultar Modal */
                $("#modalmantenimiento").modal('hide');
                $('#usuario_data').DataTable().ajax.reload();

                /* TODO:Mensaje de Confirmacion */
                swal({
                    title: "HelpDesk!",
                    text: "Actualizado Correctamente.",
                    type: "success",
                    confirmButtonClass: "btn-success"
                });
            }else if(datos=="0"){
                $("#usu_correo").addClass("form-control-error");
                $("<small class='text-muted text-danger'>El Registro ya existe</small>").insertAfter("#usu_correo");
            }
        }
    }); 
}

$(document).ready(function() {
  tabla = $("#usuario_data").dataTable({
      aProcessing: true,
      aServerSide: true,
      dom: "Bfrtip",
      searching: true,
      lengthChange: false,
      colReorder: true,
      buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
      ajax: {
        url: "../../controller/usuario.php?op=listar",
        type: "post",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      ordering: false,
      bDestroy: true,
      responsive: true,
      bInfo: true,
      iDisplayLength: 10,
      autoWidth: false,
      language: {
        sProcessing: "Procesando...",
        sLengthMenu: "Mostrar _MENU_ registros",
        sZeroRecords: "No se encontraron resultados",
        sEmptyTable: "Ningún dato disponible en esta tabla",
        sInfo: "Mostrando un total de _TOTAL_ registros",
        sInfoEmpty: "Mostrando un total de 0 registros",
        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
        sInfoPostFix: "",
        sSearch: "Buscar:",
        sUrl: "",
        sInfoThousands: ",",
        sLoadingRecords: "Cargando...",
        oPaginate: {
          sFirst: "Primero",
          sLast: "Último",
          sNext: "Siguiente",
          sPrevious: "Anterior",
        },
        oAria: {
          sSortAscending:
            ": Activar para ordenar la columna de manera ascendente",
          sSortDescending:
            ": Activar para ordenar la columna de manera descendente",
        }
      }
    })
    .DataTable();
});

function editar(usu_id) {
    $("#mdltitulo").html("Editar Registro");
    $("#usu_correo").removeClass("form-control-error");
    $("#usu_correo + small").remove();

    $.post("../../controller/usuario.php?op=mostrar", {usu_id : usu_id}, function (data) {
        data = JSON.parse(data);

        $("#usu_id").val(data.usu_id);
        $("#usu_nom").val(data.usu_nom);
        $("#usu_ape").val(data.usu_ape);
        $("#usu_correo").val(data.usu_correo);
        $("#usu_pass").val(data.usu_pass);
        $("#rol_id").val(data.rol_id).trigger('change');

        cargarSectores(function() {
    $("#sec_id").val(data.sec_id).trigger('change'); // Se asigna después de que el select esté lleno
});


        // Bloquear campos de correo y pass solo al editar
        $("#usu_correo, #usu_pass").prop("readonly", true).css({
            "background-color": "#e9ecef",
            "cursor": "not-allowed"
        });
    });

    $("#modalmantenimiento").modal("show");
}

// Al abrir el modal para crear un nuevo usuario
$(document).on("click", "#btnnuevo", function(){
    $("#mdltitulo").html("Nuevo Registro");
    $("#usuario_form")[0].reset();
    $("#usu_correo").removeClass("form-control-error");
    $("#usu_correo + small").remove();

    // Asegurarse de que los campos estén desbloqueados al crear
    $("#usu_correo, #usu_pass").prop("readonly", false).css({
        "background-color": "",
        "cursor": "auto"
    });

    $("#modalmantenimiento").modal("show");
    cargarSectores(function() {
        $("#sec_id").val("6").trigger('change');
    });
});


function cargarSectores(callback) {
    $.ajax({
        url: "../../controller/usuario.php?op=get_sectores",
        type: "get",
        dataType: "json",
        success: function (data) {
            var html = '<option value="">Seleccionar</option>';
            data.forEach(function(sector) {
                html += '<option value="'+sector.sec_id+'">'+sector.sec_nom+'</option>';
            });
            $("#sec_id").html(html).trigger('change');

            if (typeof callback === "function") {
                callback(); // Llamamos al callback una vez que el select está cargado
            }
        }
    });
}



function eliminar(usu_id) {
  swal(
    {
      title: "ATENCION!",
      text: "¿Esta seguro de cerrar el registro?",
      type: "error",
      showCancelButton: true,
      confirmButtonClass: "btn-danger",
      confirmButtonText: "Si",
      cancelButtonText: "No",
      closeOnConfirm: false,
    },
    function (isConfirm) {
      if (isConfirm) {
        $.post(
          "../../controller/usuario.php?op=eliminar",
          { usu_id: usu_id },
          function (data) {}
        );

        $("#usuario_data").DataTable().ajax.reload(); // Recarga la tabla despues de la funcion

        swal({
          title: "Registro eliminado",
          text: "La acción se realizo correctamente",
          type: "success",
          confirmButtonClass: "btn-success",
        });
      }
    }
  );
}

$(document).on("click", "#btnnuevo", function(){
  $("#mdltitulo").html("Nuevo Registro");
  $("#usuario_form")[0].reset();
  $("#usu_correo").removeClass("form-control-error");
  $("#usu_correo + small").remove();
  $("#modalmantenimiento").modal("show");
});

init();
