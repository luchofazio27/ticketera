var tabla;
var usu_id = $("#user_idx").val();
var rol_id = $("#rol_idx").val();

function init() {
  $("#ticket_form").on("submit", function (e) {
    guardar(e);
  });
}

$(document).ready(function () {

  $.post("../../controller/categoria.php?op=combo", function (data, status) {
    $("#cat_id").html(data); //$('#cat_id'): Esto es un selector de jQuery que busca un elemento en el HTML con el ID cat_id
    //.html(data): Este método de jQuery reemplaza el contenido HTML de ese elemento con el valor de data, que es la respuesta que el servidor envió
    //Esta línea realiza una solicitud POST al servidor, recupera datos (probablemente HTML) y luego inserta esos datos en un elemento de la página con el ID cat_id
  });

  $.post("../../controller/prioridad.php?op=combo", function (data, status) {
    $("#prio_id").html(data); //$('#prio_id'): Esto es un selector de jQuery que busca un elemento en el HTML con el ID cat_id
    //.html(data): Este método de jQuery reemplaza el contenido HTML de ese elemento con el valor de data, que es la respuesta que el servidor envió
    //Esta línea realiza una solicitud POST al servidor, recupera datos (probablemente HTML) y luego inserta esos datos en un elemento de la página con el ID cat_id
  });

  $.post("../../controller/usuario.php?op=combo", function (data) {
    //Llenamos el combo usuario asignar
    $("#usu_asig").html(data);
  });
  
  if (rol_id == 1) {
    // Verificamos si el rol id es de usuario
    $('#viewuser').hide();
    tabla = $("#ticket_data")
      .dataTable({
        aProcessing: true,
        aServerSide: true,
        dom: "Bfrtip",
        searching: true,
        lengthChange: false,
        colReorder: true,
        buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
        ajax: {
          url: "../../controller/ticket.php?op=listar_x_usu_historial",
          type: "post",
          dataType: "json",
          data: { usu_id: usu_id },
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
          },
        },
      })
      .DataTable();
  } else {
    var tick_titulo = $("#tick_titulo").val();
    var prio_id = $("#prio_id").val();
    var cat_id = $("#cat_id").val();

    listardatatable(tick_titulo, cat_id, prio_id);
  }

});


$(document).on("click", ".btn-inline", function () {
  const ciphertext = $(this).data("ciphertext");
  window.location.href = "https://ticketsver.online/ticketera/view/DetalleTicket/?ID=" + ciphertext;
});


function asignar(tick_id) {
  $.post("../../controller/ticket.php?op=mostrar_noencry", {tick_id: tick_id}, function (data) {
      data = JSON.parse(data); //Parseamos la data /
      $("#tick_id").val(data.tick_id);
      $("#mdltitulo").html("Asignar Agente");
      $("#modalasignar").modal("show");
  });
}


function guardar(e) {
  e.preventDefault(); // evita que se guarde dos veces en un posible doble click
  $('#btnguardar').prop('disabled', true); // Deshabilita el botón para evitar múltiples envíos
  $('#btnguardar').html('<i class="fa fa-spinner fa-spin"></i> Guardando...'); // Cambia el texto del botón para indicar que se está guardando
  var formData = new FormData($("#ticket_form")[0]); //Se crea el objeto formdata. Prepara datos de un form para enviarlos por una solicitud AJAX
  $.ajax({
    url: "../../controller/ticket.php?op=asignar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (datos) {
      $('#ticket_data').DataTable().ajax.reload(); //Recarga la tabla despues de la funcion
      
      swal("Correcto!", "Asignado Correctamente", "success");
      $("#modalasignar").modal("hide"); // Ocultar el modal
      $("#ticket_data").DataTable().ajax.reload(); // Recargar el dataTable
      $('#btnguardar').prop('disabled', true); // Deshabilita el botón para evitar múltiples envíos
      $('#btnguardar').html('<i class="fa fa-spinner fa-spin"></i> Guardando...'); // Cambia el texto del botón para indicar que se está guardando
    },
  });
}

function CambiarEstado(tick_id) {
  swal(
    {
      title: "ATENCION!",
      text: "¿Esta seguro de Reabrir el Ticket?",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-warning",
      confirmButtonText: "Si",
      cancelButtonText: "No",
      closeOnConfirm: false,
      closeOnCancel: true
    },
    function (isConfirm) {

      if (!isConfirm) {
        return;
      }

      // Deshabilitar inmediatamente los botones del SweetAlert
      $(".sweet-alert button").prop("disabled", true);

      // Cambiar visualmente el botón de confirmación
      $(".sweet-alert .confirm")
        .html('<i class="fa fa-spinner fa-spin"></i> Reabriendo...')
        .prop("disabled", true);

      // Evitar cualquier otra ejecución de CambiarEstado
      if (window.reabriendoTicket) {
        return;
      }

      window.reabriendoTicket = true;

      $.post(
        "../../controller/ticket.php?op=reabrir",
        {
          tick_id: tick_id,
          usu_id: usu_id
        },
        function (data) {

          swal(
            {
              title: "Ticket Reabierto",
              text: "La acción se realizó correctamente.",
              type: "success",
              confirmButtonClass: "btn-success",
              confirmButtonText: "Aceptar"
            },
            function () {

              window.location.href = "../ConsultarTicket/";

            }
          );

        }
      )
      .fail(function () {

        // Si hubo un error, permitimos intentar nuevamente
        window.reabriendoTicket = false;

        swal(
          {
            title: "Error",
            text: "No se pudo reabrir el ticket. Intente nuevamente.",
            type: "error",
            confirmButtonClass: "btn-danger"
          }
        );

      });

    }
  );
}

$(document).on("click","#btnfiltrar", function(){
  limpiar(); //Llama a la funcion que limpia los campos

  var tick_titulo = $("#tick_titulo").val(); //Obtiene el valor del campo de texto con id tick_titulo
  var cat_id = $("#cat_id").val(); //Obtiene el valor del campo de texto con id cat_id 
  var prio_id = $("#prio_id").val(); //Obtiene el valor del campo de texto con id prio_id 

  listardatatable(tick_titulo, cat_id, prio_id); //Llama a la funcion que lista los tickets
});

$(document).on("click","#btntodo", function(){
  limpiar(); //Llama a la funcion que limpia los campos

  $("#tick_titulo").val('');
  $("#cat_id").val('').trigger("change"); //Llama a la funcion que limpia los campos
  $("#prio_id").val('').trigger("change"); //Llama a la funcion que limpia los campos

  listardatatable('', '', ''); //Llama a la funcion que lista los tickets
});

$(document).on("click", "#btnmistickets", function () {
    var usu_id = $("#user_idx").val();

    // Opcional: limpiar UI de filtros
    $("#tick_titulo").val('');
    $("#cat_id").val('').trigger("change");
    $("#prio_id").val('').trigger("change");

    // Cambiar URL a la nueva acción y cargar
    tabla.ajax.url("../../controller/ticket.php?op=listar_mis_tickets_historial");

    // Inyectar usu_asig en la siguiente request
    tabla.one('preXhr.dt', function (e, settings, data) {
        data.usu_asig = usu_id;
    });

    tabla.ajax.reload(null, false); // recarga sin resetear paginacion
});


function listardatatable(tick_titulo, cat_id, prio_id){
  tabla = $("#ticket_data")
      .dataTable({
        aProcessing: true,
        aServerSide: true,
        dom: "Bfrtip",
        searching: true,
        lengthChange: false,
        colReorder: true,
        buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
        ajax: {
          url: "../../controller/ticket.php?op=listar_filtro_historial",
          type: "post",
          dataType: "json",
          data: { tick_titulo:tick_titulo, cat_id:cat_id, prio_id:prio_id },
          error: function (e) {
            console.log(e.responseText);
          },
        },
        ordering: true,
        order: [[0, "desc"]],
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
          },
        },
      }).DataTable().ajax.reload(); //Recarga la tabla despues de la funcion
}

function limpiar() {
  $('#table').html(
    "<table id='ticket_data' class='table table-bordered table-striped table-vcenter js-dataTable-full'>"+
						"<thead>"+
              "<tr>"+
                "<th style='width: 5%;'>Nro.Ticket</th>"+
                "<th class='d-none d-sm-table-cell' style='width: 15%;'>Solicitante</th>"+
                "<th class='d-none d-sm-table-cell' style='width: 5%;'>Sector</th>"+
                "<th style='width: 5%;'>Categoria</th>"+
                "<th class='d-none d-sm-table-cell' style='width: 15%;'>Titulo</th>"+
                "<th class='d-none d-sm-table-cell' style='width: 5%;'>Prioridad</th>"+
                "<th class='d-none d-sm-table-cell' style='width: 5%;'>Estado</th>"+
                "<th class='d-none d-sm-table-cell' style='width: 7%;'>Fecha Creación</th>"+
                "<th class='d-none d-sm-table-cell' style='width: 7%;'>Fecha Cierre</th>"+
                "<th class='d-none d-sm-table-cell' style='width: 7%;'>Soporte</th>"+
                "<th class='text-center' style='width: 1%;'></th>"+
              "</tr>"+
            "</thead>"+
            "<tbody>"+

            "</tbody>"+
          "</table>");
  }
          

init();