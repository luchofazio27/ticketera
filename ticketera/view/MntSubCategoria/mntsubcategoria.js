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
        url: "../../controller/subcategoria.php?op=guardaryeditar",
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
                $("#cats_nom").addClass("form-control-error");
                $("<small class='text-muted text-danger'>El Registro ya existe</small>").insertAfter("#cats_nom");
            }
        }
    }); 
}

$(document).ready(function() {

  $.post("../../controller/categoria.php?op=combo", function (data, status) {
    $("#cat_id").html(data);
  });

  // 🔹 Cargar el combo de sectores al iniciar
$.post("../../controller/sector.php?op=combo", function (data, status) {
  $("#sec_id").html(data);
});

// 🔹 Cuando cambia el sector, cargar sus categorías correspondientes
$("#sec_id").on("change", function () {
  var sec_id = $(this).val();
  $.post("../../controller/categoria.php?op=combo_categoria_x_sector", { sec_id: sec_id }, function (data, status) {
    $("#cat_id").html(data);
  });
});


  tabla = $("#usuario_data").dataTable({
      aProcessing: true,
      aServerSide: true,
      dom: "Bfrtip",
      searching: true,
      lengthChange: false,
      colReorder: true,
      buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
      ajax: {
        url: "../../controller/subcategoria.php?op=listar",
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

function editar(cats_id) {
  $("#mdltitulo").html("Editar Registro");
  $("#cats_nom").removeClass("form-control-error");
  $("#cats_nom + small").remove();

  $.post("../../controller/subcategoria.php?op=mostrar", { cats_id: cats_id }, function (data) {
    data = JSON.parse(data);

    $("#cats_id").val(data.cats_id);
    $("#sec_id").val(data.secs_id).trigger("change"); // primero el sector

    // Esperar que se carguen las categorías de ese sector antes de asignar
    setTimeout(function() {
      $("#cat_id").val(data.cat_id).trigger("change");
    }, 300);

    $("#cats_nom").val(data.cats_nom);
  });

  $("#modalmantenimiento").modal("show");
}


function eliminar(cats_id) {
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
          "../../controller/subcategoria.php?op=eliminar",
          { cats_id: cats_id },
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
  $("#cats_id").val("");
  $("#cat_id").val("").trigger("change");
  $("#mdltitulo").html("Nuevo Registro");
  $("#usuario_form")[0].reset();
  $("#cats_nom").removeClass("form-control-error");
  $("#cats_nom + small").remove();
  $("#modalmantenimiento").modal("show");
});

init();
