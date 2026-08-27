var tabla;
var usu_id = $("#user_idx").val();
var rol_id = $("#rol_idx").val();

function init() {
  $("#ticket_form").on("submit", function (e) {
    guardar(e);
  });
}

$(document).ready(function () {

  $.post("../../controller/categoria.php?op=combo_filtro", function (data, status) {
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
          url: "../../controller/ticket.php?op=listar_x_usu",
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

// --- BLOQUE NUEVO PARA CARGAR SECTOR Y USUARIOS DEPENDIENTES ---
$('#modalasignar').on('show.bs.modal', function () {
  // Limpiar selects al abrir el modal
  $("#soporte_sec_id").html('<option value=""></option>');
  $("#usu_asig").html('<option value="">Seleccionar usuario</option>');

  // Cargar los sectores de soporte cuando se abre el modal
  $.post("../../controller/sector.php?op=combo_soporte", function (data) {
    $("#soporte_sec_id").html('<option value="">Seleccionar sector</option>' + data);
  });
});


// Cuando cambie el sector, cargar los usuarios de ese sector
$("#soporte_sec_id").change(function () {
  var sec_id = $(this).val();

  if (sec_id !== "") {
    $.post("../../controller/usuario.php?op=combo_x_sector", { sec_id: sec_id }, function (data) {
      $("#usu_asig").html("<option value=''>Seleccionar usuario</option>" + data);
    });
  } else {
    $("#usu_asig").html("<option value=''>Seleccionar usuario</option>");
  }
});



function guardar(e) {
  e.preventDefault();
  $('#btnguardar').prop('disabled', true);
  $('#btnguardar').html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

  var formData = new FormData($("#ticket_form")[0]);

  // opcional: debug
  console.log("FormData tick_id:", $("#tick_id").val(), "soporte_sec_id:", $("#soporte_sec_id").val(), "usu_asig:", $("#usu_asig").val());

  $.ajax({
    url: "../../controller/ticket.php?op=asignar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (respuesta) {
      // parseamos JSON seguro
      let res;
      try {
        res = (typeof respuesta === "string") ? JSON.parse(respuesta) : respuesta;
      } catch (err) {
        console.error("Asignar error response:", respuesta);
        $('#btnguardar').prop('disabled', false);
        $('#btnguardar').html('Asignar');
        swal("Error", "Respuesta inesperada del servidor", "error");
        return;
      }

      if (res.ok) {
        $('#ticket_data').DataTable().ajax.reload();
        swal("Correcto!", "Asignado Correctamente", "success");
        $("#modalasignar").modal("hide");
      } else if (res.error) {
        swal("Error", res.error, "error");
      } else {
        swal("Error", "Ocurrió un error al asignar", "error");
      }

      $('#btnguardar').prop('disabled', false);
      $('#btnguardar').html('Asignar');
    },
    error: function (xhr, status, error) {
      console.error("Asignar AJAX error:", xhr.responseText);
      $('#btnguardar').prop('disabled', false);
      $('#btnguardar').html('Asignar');
      swal("Error", "Error en la petición: " + error, "error");
    }
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
    },
    function (isConfirm) {
      if (isConfirm) {
        $.post(
          "../../controller/ticket.php?op=reabrir",
          { tick_id: tick_id, usu_id: usu_id },
          function (data) {}
        );

        $("#ticket_data").DataTable().ajax.reload(); // Recarga la tabla despues de la funcion

        swal({
          title: "Ticket Abierto",
          text: "La acción se realizo correctamente",
          type: "success",
          confirmButtonClass: "btn-success",
        });
      }
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
    tabla.ajax.url("../../controller/ticket.php?op=listar_mis_tickets");

    // Inyectar usu_asig en la siguiente request
    tabla.one('preXhr.dt', function (e, settings, data) {
        data.usu_asig = usu_id;
    });

    tabla.ajax.reload(null, false); // recarga sin resetear paginacion
});

$(document).on("click", "#btnTicketsCreados", function () {
    var usu_id = $("#user_idx").val();

    // Opcional: limpiar UI de filtros
    $("#tick_titulo").val('');
    $("#cat_id").val('').trigger("change");
    $("#prio_id").val('').trigger("change");

    // Cambiar URL a la nueva acción y cargar
    tabla.ajax.url("../../controller/ticket.php?op=listar_mis_tickets2");

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
          url: "../../controller/ticket.php?op=listar_filtro",
          type: "post",
          dataType: "json",
          data: { tick_titulo:tick_titulo, cat_id:cat_id, prio_id:prio_id },
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
                "<th style='width: 15%;'>Categoria</th>"+
                "<th class='d-none d-sm-table-cell' style='width: 15%;'>Titulo</th>"+
                "<th class='d-none d-sm-table-cell' style='width: 2%;'>Prioridad</th>"+
                "<th class='d-none d-sm-table-cell' style='width: 5%;'>Estado</th>"+
                "<th class='d-none d-sm-table-cell' style='width: 7%;'>Fecha Creación</th>"+
                "<th class='d-none d-sm-table-cell' style='width: 7%;'>Soporte</th>"+
                "<th class='text-center' style='width: 1%;'></th>"+
              "</tr>"+
            "</thead>"+
            "<tbody>"+

            "</tbody>"+
          "</table>");
  }
          
// === Apertura automática del modal de asignar cuando llegamos desde DetalleTicket ===
(function autoOpenAssignModalFromDetail() {
  try {
    const params = new URLSearchParams(window.location.search);
    const abrir = params.get("abrir");
    const ticketParam = params.get("ticket"); // número puro que enviamos desde detalle

    if (!abrir || abrir !== "asignar") return; // nada que hacer

    console.log("🟢 Abrir modal de asignar automáticamente (desde detalleticket)");

    // Buscamos el botón "Asignar" en la tabla / UI para disparar la misma lógica que cuando se hace click manual.
    // Esperamos un poco si el DOM aún se está construyendo (datatable u otros elementos).
    const waitForButton = setInterval(() => {
      const $btn = $("#btnguardar").length ? $("#btnguardar") : null; // boton submit del modal
      const $openAssignBtn = $(".btn-inline[aria-label='Asignar']").first(); // fallback (por si usás un botón con aria)
      // En la vista consultarticket el modal se abre por la función asignar(tick_id) que usa #modalasignar.
      // Lo más robusto: abrir el modal directamente y precargar #tick_id si viene ticketParam.

      // Cuando esté el modal y el hidden #tick_id existente en DOM, procedemos.
      if ($("#modalasignar").length && $("#tick_id").length) {
        clearInterval(waitForButton);

        // Si vino number, setearlo en el hidden para que el form lo envíe
        if (ticketParam) {
          console.log("🏷️ Pasando ticket number al modal:", ticketParam);
          $("#tick_id").val(ticketParam);
          $("#mdltitulo").html("Asignar Agente - Ticket " + ticketParam);
        } else {
          $("#mdltitulo").html("Asignar Agente");
        }

        // Cargar combos igual que cuando se abre manualmente
        $("#soporte_sec_id").html('<option value="">Cargando...</option>');
        $.post("../../controller/sector.php?op=combo_soporte", function (data) {
          $("#soporte_sec_id").html('<option value="">Seleccionar sector</option>' + data);
          if ($("#soporte_sec_id").hasClass("select2-hidden-accessible")) {
            $("#soporte_sec_id").trigger("change.select2");
          }
        });

        $("#usu_asig").html('<option value="">Seleccionar usuario</option>');

        // finalmente abrimos el modal
        $("#modalasignar").modal("show");

        // opcional: limpiar el parámetro de URL (historia) para que no vuelva a disparar si recargan
        try {
          const url = new URL(window.location);
          url.searchParams.delete('abrir');
          url.searchParams.delete('ticket');
          window.history.replaceState({}, document.title, url.toString());
        } catch (e) {
          // no crítico
        }

      } else {
        console.log("⏳ Esperando a que cargue el modal y el hidden #tick_id...");
      }
    }, 200);
  } catch (err) {
    console.error("Error en autoOpenAssignModalFromDetail:", err);
  }
})();



init();