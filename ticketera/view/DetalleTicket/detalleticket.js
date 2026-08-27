function init() {}

$(document).ready(function () {
  const url = window.location.href; // Obtiene la URL actual
  const params = new URLSearchParams(new URL(url).search); // Crea un objeto URLSearchParams a partir de la URL
  const tick_id = params.get("ID"); // Obtiene el valor del parámetro 'ID' de la URL
  const decoded_id = decodeURIComponent(tick_id); // Decodifica el valor del ID para manejar caracteres especiales
  const id = decoded_id.replace(/\s/g, "+"); // Reemplaza los espacios en blanco con '+' para asegurar que el ID sea válido en la URL


  mostraryvalidad(id);

  $("#tickd_descrip").summernote({
    height: 150,
    lang: "es-ES",
    popover: {
      image: [],
      link: [],
      air: [],
    },
    callbacks: {
      onImageUpload: function (image) {
        console.log("Image detect...");
        myimagetreat(image[0]);
      },
      onPaste: function (e) {
        console.log("Text detect...");
      },
    },
    toolbar: [
      ["style", ["bold", "italic", "underline", "clear"]],
      ["font", ["strikethrough", "superscript", "subscript"]],
      ["fontsize", ["fontsize"]],
      ["color", ["color"]],
      ["para", ["ul", "ol", "paragraph"]],
      ["height", ["height"]],
    ],
  });

  $("#tickd_descripusu").summernote({
    height: 150,
    lang: "es-ES",
    toolbar: [
      ["style", ["bold", "italic", "underline", "clear"]],
      ["font", ["strikethrough", "superscript", "subscript"]],
      ["fontsize", ["fontsize"]],
      ["color", ["color"]],
      ["para", ["ul", "ol", "paragraph"]],
      ["height", ["height"]],
    ],
  });

  $("#tickd_descripusu").summernote("disable");

  tabla = $("#documentos_data")
    .dataTable({
      aProcessing: true,
      aServerSide: true,
      dom: "Bfrtip",
      searching: false,
      lengthChange: false,
      colReorder: true,
      buttons: [],
      ajax: {
        url: "../../controller/documento.php?op=listar",
        type: "post",
        data: { tick_id: id },
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      responsive: true,
      bInfo: true,
      iDisplayLength: 10,
      autoWidth: false,
      order: [[0, "asc"]],
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

});

$(document).on("click", "#btnenviar", function () {
  // Funcion del boton enviar dentro del detalle ticket
  const url = window.location.href; // Obtiene la URL actual
  const params = new URLSearchParams(new URL(url).search); // Crea un objeto URLSearchParams a partir de la URL
  const tick_id = params.get("ID"); // Obtiene el valor del parámetro 'ID' de la URL
  const decoded_id = decodeURIComponent(tick_id); // Decodifica el valor del ID para manejar caracteres especiales
  const id = decoded_id.replace(/\s/g, "+"); // Reemplaza los espacios en blanco con '+' para asegurar que el ID sea válido en la URL
  var usu_id = $("#user_idx").val(); //Aquí se está obteniendo el valor de un elemento del DOM con el ID user_idx
  var tickd_descrip = $("#tickd_descrip").val(); //Aquí se está obteniendo el valor de un elemento del DOM con el ID tickd_descrip
  if ($("#tickd_descrip").summernote("isEmpty")) {
    swal("Advertencia!", "Descripción Vacía", "warning");
  } else {
    var formData = new FormData(); //se crea un nuevo objeto FormData, que se utiliza para construir un conjunto de pares clave/valor representando los campos del formulario y sus valores.
    formData.append("tick_id", id); //se agrega el valor de tick_id al objeto formData con la clave 'tick_id'
    formData.append("usu_id", usu_id); //se agrega el valor de usu_id al objeto formData con la clave 'usu_id'
    formData.append("tickd_descrip", tickd_descrip); //se agrega el valor de tickd_descrip al objeto formData con la clave 'tickd_descrip'
    var totalfiles = $("#fileElem").val().length; //se obtiene la longitud del valor del elemento con el ID fileElem, que representa la cantidad de archivos seleccionados
    for (var i = 0; i < totalfiles; i++) {
      //se inicia un bucle que itera sobre cada archivo seleccionado
      formData.append("files[]", $("#fileElem")[0].files[i]); //se agrega cada archivo al objeto formData con la clave 'file[]'
    }

    $.ajax({
      url: "../../controller/ticket.php?op=insertdetalle", //se especifica la URL a la que se enviará la solicitud AJAX
      type: "POST", //se especifica el método HTTP que se utilizará para la solicitud (en este caso, POST)
      data: formData, //se pasan los datos del formulario al objeto formData
      contentType: false, //se establece en false para que jQuery no intente establecer el tipo de contenido de la solicitud
      processData: false, //se establece en false para que jQuery no procese los datos antes de enviarlos
      success: function (data) {
        //se define una función de éxito que se ejecutará cuando la solicitud AJAX se complete con éxito
        console.log(data); //se imprime la respuesta del servidor en la consola
        mostraryvalidad(id); //se llama a la función mostraryvalidad pasando el valor de id como argumento
        $("#tickd_descrip").summernote("reset"); //se restablece el contenido del editor de texto enriquecido tickd_descrip a su estado inicial
        swal("Correcto!", "Registrado Correctamente", "success"); //se muestra una alerta de éxito utilizando la biblioteca SweetAlert
        $("#previewFiles").html("");        // limpiar previsualización
        $("#fileElem").val("");             // limpiar input file
        $.unblockUI(); // Desbloquea la interfaz de usuario
      },beforeSend: function(){
                    $.blockUI({
                        overlayCSS:  {
                            background: 'rgba(142, 159, 167, 0.3)',
                            opacity: 1,
                            cursor: 'wait'
                        },
                        css: {
                            width: 'auto',
                            top: '45%',
                            left: '45%'
                        },
                        message: '<div class="blockui-default-message">Espere...</div>',
                        blockMsgClass: 'block-msg-message-loader'
                    });
                },
    });
  }
});

$(document).on("click", "#btncerrarticket", function () {
  // Funcion del boton cerrar ticket
  swal(
    {
      title: "ATENCION!",
      text: "¿Esta seguro de cerrar el Ticket?",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-warning",
      confirmButtonText: "Si",
      cancelButtonText: "No",
      closeOnConfirm: false,
    },
    function (isConfirm) {
      if (isConfirm) {
        //var tick_id = getUrlParameter('ID');
        const url = window.location.href; // Obtiene la URL actual
        const params = new URLSearchParams(new URL(url).search); // Crea un objeto URLSearchParams a partir de la URL
        const tick_id = params.get("ID"); // Obtiene el valor del parámetro 'ID' de la URL
        const decoded_id = decodeURIComponent(tick_id); // Decodifica el valor del ID para manejar caracteres especiales
        const id = decoded_id.replace(/\s/g, "+"); // Reemplaza los espacios en blanco con '+' para asegurar que el ID sea válido en la URL

        $.ajax({
            url: "../../controller/ticket.php?op=update",
            type: "POST",
            data: { tick_id: id },
            success: function (datos) {
              console.log(datos); //se imprime la respuesta del servidor en la consola
              mostraryvalidad(id);
              swal("Correcto!", "Ticket Cerrado", "success");
              $.unblockUI(); // Desbloquea la interfaz de usuario
            },beforeSend: function(){
                    $.blockUI({
                        overlayCSS:  {
                            background: 'rgba(142, 159, 167, 0.3)',
                            opacity: 1,
                            cursor: 'wait'
                        },
                        css: {
                            width: 'auto',
                            top: '45%',
                            left: '45%'
                        },
                        message: '<div class="blockui-default-message">Espere...</div>',
                        blockMsgClass: 'block-msg-message-loader'
                    });
                },
        });

        

        /*
        $.post(
          "../../controller/email.php?op=ticket_cerrado",
          { tick_id: id },
          function (data) {}
        );
        */

        //listardetalle(tick_id);

        swal({
          title: "Ticket Cerrado",
          text: "Ticket cerrado correctamente",
          type: "success",
          confirmButtonClass: "btn-success",
        });
      }
    }
  );
});

$(document).on("click", "#btncomentar_cerrar", function () {
  const url = window.location.href;
  const params = new URLSearchParams(new URL(url).search);
  const tick_id = params.get("ID");
  const decoded_id = decodeURIComponent(tick_id);
  const id = decoded_id.replace(/\s/g, "+");
  var usu_id = $("#user_idx").val();
  var tickd_descrip = $("#tickd_descrip").val();

  if ($("#tickd_descrip").summernote("isEmpty")) {
    swal("Advertencia!", "Descripción Vacía", "warning");
    return;
  }

  swal(
    {
      title: "Confirmar",
      text: "¿Desea agregar este comentario y cerrar el ticket?",
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, comentar y cerrar",
      cancelButtonText: "Cancelar",
      closeOnConfirm: false,
    },
    function (isConfirm) {
      if (!isConfirm) return;

      // Cerramos el swal antes de bloquear y ejecutar AJAX
      swal.close();

      var formData = new FormData();
      formData.append("tick_id", id);
      formData.append("usu_id", usu_id);
      formData.append("tickd_descrip", tickd_descrip);

      var totalfiles = $("#fileElem").val().length;
      for (var i = 0; i < totalfiles; i++) {
        formData.append("files[]", $("#fileElem")[0].files[i]);
      }

      $.ajax({
        url: "../../controller/ticket.php?op=comentar_cerrar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
          $.blockUI({
            overlayCSS: {
              background: "rgba(142, 159, 167, 0.3)",
              opacity: 1,
              cursor: "wait",
            },
            css: {
              width: "auto",
              top: "45%",
              left: "45%",
            },
            message:
              '<div class="blockui-default-message">Procesando...</div>',
            blockMsgClass: "block-msg-message-loader",
          });
        },
        success: function (data) {
          console.log("✅ comentar_cerrar respuesta:", data);
          mostraryvalidad(id);
          $("#tickd_descrip").summernote("reset");
          swal("Correcto!", "Ticket comentado y cerrado", "success");
        },
        complete: function () {
          $.unblockUI();
        },
      });
    }
  );
});



function mostraryvalidad(id) {
      $.post(
    "../../controller/ticket.php?op=listardetalle",
    { tick_id: id },
    function (data) {
      //esta línea de código es enviar una solicitud POST al archivo ticket.php con el parámetro "listardetalle"
      //se le envia un objeto de datos al servidor con la variable tick_id para que identifique el ticket por el cual se consulta
      //la funcion se ejecuta una vez obtenemos la respuesta que se almacenara en el parametro "data"
      $("#lbldetalle").html(data); //.html(data): Este método de jQuery reemplaza el contenido HTML de ese elemento con el valor de data
    }
  );

  $.post(
    "../../controller/ticket.php?op=mostrar",
    { tick_id: id },
    function (data) {
      data = JSON.parse(data); //JSON.parse(data) en JavaScript analiza una cadena de texto en formato JSON y la transforma en un objeto JavaScript.
      $("#lblestado").html(data.tick_estado);
      $("#lblnomusuario").html(data.usu_nom + " " + data.usu_ape);

if (
    data.usu_crea &&
    data.usu_crea_nom &&
    data.usu_crea_ape &&
    String(data.usu_crea) !== String(data.usu_id)
) {
    $("#lblnomcreador")
        .html("Creado por: " + data.usu_crea_nom + " " + data.usu_crea_ape)
        .show();
} else {
    $("#lblnomcreador").hide();
}
      $("#lblfechcrea").html(data.fech_crea);
      $("#lblnomidticket").html("Detalle Ticket " + data.tick_id);
      $("#cat_nom").val(data.cat_nom);
      $("#cats_nom").val(data.cats_nom);
      $("#tick_titulo").val(data.tick_titulo);
      $("#tickd_descripusu").summernote("code", data.tick_descrip);
      $("#prio_nom").val(data.prio_nom);
      if (data.tick_estado_texto == "Cerrado") {
    $("#pnldetalle").hide();
    $("#pnlreabrir").show();
} else {
    $("#pnldetalle").show();
    $("#pnlreabrir").hide();
}
    }
  );

}

// =========================
// BOTÓN: redirigir a Consultarticket para abrir modal asignar
// =========================
$(document).on("click", "#btnasignar_detalle", function () {
  console.log("🟢 Botón Asignar presionado (detalle)");

  // Intentamos leer el ID numérico desde el elemento que ya carga el detalle:
  // #lblnomidticket tiene "Detalle Ticket <ID>" (o similar)
  const rawText = $("#lblnomidticket").text() || "";
  // Extraemos el primer grupo de dígitos que encontremos
  const match = rawText.match(/(\d+)/);
  const ticketNum = match ? match[1] : null;

  if (!ticketNum) {
    // Si no encontramos, intentamos tomarlo desde mostrar_noencry o desde un hidden si tenés uno:
    // fallback: tratar de obtener el hidden #tick_id (puede estar encriptado, así que no siempre sirve)
    const hidden = $("#tick_id").val();
    if (hidden && /^\d+$/.test(hidden)) {
      // si accidentalmente está el número puro en el hidden
      window.location.href = `../ConsultarTicket/?ticket=${encodeURIComponent(hidden)}&abrir=asignar`;
      return;
    }

    swal("Error", "No se pudo obtener el número de ticket para redirigir.", "error");
    return;
  }

  // Construimos destino con ticket numérico (no encriptado) y marca abrir=asignar
  const destino = `../ConsultarTicket/?ticket=${encodeURIComponent(ticketNum)}&abrir=asignar`;
  console.log("➡️ Redirigiendo a:", destino);

  // redirigimos (usar relative según tu estructura; si detalleticket está en view/DetalleTicket, esta ruta va una carpeta hacia arriba)
  window.location.href = destino;
});

// ===============================
// BOTÓN: REABRIR TICKET DESDE DETALLE
// ===============================
$(document).on("click", "#btnreabrirticket", function () {

  // Evitar múltiples clics mientras se procesa
  if (window.reabriendoTicket) {
    return;
  }

  const url = window.location.href;
  const params = new URLSearchParams(new URL(url).search);
  const tick_id = params.get("ID");
  const decoded_id = decodeURIComponent(tick_id);
  const id = decoded_id.replace(/\s/g, "+");

  var usu_id = $("#user_idx").val();

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

      // Evitar doble/triple reapertura
      window.reabriendoTicket = true;

      // Deshabilitar botones inmediatamente
      $(".sweet-alert button").prop("disabled", true);

      // Mostrar que se está procesando
      $(".sweet-alert .confirm")
        .html('<i class="fa fa-spinner fa-spin"></i> Reabriendo...')
        .prop("disabled", true);

      $.post(
        "../../controller/ticket.php?op=reabrir",
        {
          tick_id: id,
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

              // Recargar el mismo detalle
              window.location.reload();

            }
          );

        }
      )
      .fail(function () {

        // Si hubo un error permitimos volver a intentarlo
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
});

// ===============================
// PREVISUALIZAR ARCHIVOS + ELIMINAR ANTES DE ENVIAR
// ===============================
let selectedFiles = [];

$("#fileElem").on("change", function (e) {
    selectedFiles = Array.from(e.target.files);
    renderPreview();
});

// Renderiza la vista previa con botón de borrar
function renderPreview() {
    let html = "";
    selectedFiles.forEach((file, index) => {
        html += `
            <div class="file-preview-item" style="display:flex; align-items:center; margin-bottom:5px;">
                <span style="flex-grow:1;">📄 ${file.name}</span>
                <button class="btn btn-sm btn-danger remove-file" data-index="${index}">X</button>
            </div>
        `;
    });

    $("#previewFiles").html(html);
}

// Quitar archivo antes de enviar
$(document).on("click", ".remove-file", function () {
    const index = $(this).data("index");
    selectedFiles.splice(index, 1);      // Eliminar del array
    updateInputFiles();                  // Actualizar input type file
    renderPreview();                     // Volver a renderizar
});

// Actualizar el input file con los archivos restantes
function updateInputFiles() {
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    document.getElementById("fileElem").files = dt.files;
}


init();