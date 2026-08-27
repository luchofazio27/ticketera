var tabla;

function init() {
  $("#usuario_form").on("submit", function(e){
    guardaryeditar(e);
    $("#usu_id").val("");
  });
}

function guardaryeditar(e){
  e.preventDefault(); // evita que se guarde dos veces en un posible doble click
  var formData = new FormData($("#usuario_form")[0]); //Se crea el objeto formdata. Prepara datos de un form para enviarlos por una solicitud AJAX
  $.ajax({
    url: "../../controller/categoria.php?op=guardaryeditar",
    type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (datos) {
        console.log(datos);
        if(datos == "1"){
          $("#usuario_form")[0].reset(); // Limpia el form
          $("#modalmantenimiento").modal("hide"); // Ocultar el form
          $("#usuario_data").DataTable().ajax.reload(); // Recargar el dataTable
          swal({
            title: "Completado",
            text: "Registrado correctamente",
            type: "success",
            confirmButtonClass: "btn-success",
        });
        } else if(datos == "2"){
          $("#usuario_form")[0].reset(); // Limpia el form
          $("#modalmantenimiento").modal("hide"); // Ocultar el form
          $("#usuario_data").DataTable().ajax.reload(); // Recargar el dataTable
          swal({
            title: "Completado",
            text: "Actualizado correctamente",
            type: "success",
            confirmButtonClass: "btn-success",
        });
        } else if(datos == "0"){
          $("#cat_nom").addClass("form-control-error");
          $("<small class='text-muted text-danger'>El Registro ya existe</small>").insertAfter("#cat_nom");
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
        url: "../../controller/categoria.php?op=listar",
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

function editar(cat_id) {
  $("#mdltitulo").html("Editar Registro");
  $("#cat_nom").removeClass("form-control-error");
  $("#cat_nom + small").remove();
  $.post("../../controller/categoria.php?op=mostrar", {cat_id : cat_id}, function (data) {
      console.log(data);
      data = JSON.parse(data); // Transforma la data que traemos de texto a json
      $("#cat_id").val(data.cat_id);
      $("#cat_nom").val(data.cat_nom);
      $("#sec_id").val(data.sec_id);
    });
  $("#modalmantenimiento").modal("show");
}

function eliminar(cat_id) {
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
          "../../controller/categoria.php?op=eliminar",
          { cat_id: cat_id },
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
  $("#cat_id").val("");
  $("#mdltitulo").html("Nuevo Registro");
  $("#usuario_form")[0].reset();
  $("#cat_nom").removeClass("form-control-error");
  $("#cat_nom + small").remove();
  $("#modalmantenimiento").modal("show");
});

$(document).ready(function() {
  // Cargar combo de sectores al abrir el modal
  $.post("../../controller/sector.php?op=combo", function (data) {
    $("#sec_id").html(data);
  });
});


init();
