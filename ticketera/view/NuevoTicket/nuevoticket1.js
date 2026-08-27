// init: conecta el submit al handler
function init() {
  $("#ticket_form").on("submit", function (e) {
    guardaryeditar(e);
  });
}

//#############################################################
// Summernote (sin cambios)
$(document).ready(function () {
  $(document).ready(function() {
    console.log(">>> Cargando sectores soportistas...");
    $.post("../../controller/sector.php?op=combo_soporte", function(data, status){
        console.log("Status:", status);
        console.log("Respuesta cruda:", data);
        $("#sec_id").html(data); // reemplaza todo el contenido del select
    }).fail(function(xhr, status, err){
        console.error("Error al cargar sectores soportistas:", status, err);
    });
});


  $("#tick_descrip").summernote({
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

    const clipboardData = (e.originalEvent || e).clipboardData || window.clipboardData;

    if (!clipboardData) {
        return;
    }

    const items = clipboardData.items;

    if (!items) {
        return;
    }

    for (let i = 0; i < items.length; i++) {

        if (items[i].type.indexOf("image") !== -1) {

            e.preventDefault();

            swal(
                "Atención",
                "No se permite pegar imágenes directamente en la descripción. Adjunte la imagen como archivo.",
                "warning"
            );

            return false;
        }
    }
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


// Cuando cambia la categoría
$("#cat_id").change(function () {
  var cat_id = $(this).val();
  $.post(
    "../../controller/subcategoria.php?op=combo",
    { cat_id: cat_id },
    function (data, status) {
      $("#cats_id").html(data);
      // luego de recargar subcategorías, aplicamos reglas (por si cambió a CAI)
      setTimeout(applyCaiRules, 120);
    }
  );
});

// Cuando cambia el select de sector
$("#sec_id").change(function () {
  var sec_id = $(this).val();

  // ✅ sincroniza el hidden dentro del mismo bloque
  $("#soporte_sec_id").val(sec_id || "");

  if (sec_id !== "") {
    $.post(
      "../../controller/categoria.php?op=combo_categoria_x_sector",
      { sec_id: sec_id },
      function (data) {
        $("#cat_id").html(data);
      }
    );
  } else {
    $("#cat_id").html("");
  }
});




    //-----------------------------------------------------------
  // Lógica para "Requerimiento Arquitectura y Visual"
  //-----------------------------------------------------------

  
  // Detecta cambio en la categoría
  $("#cat_id").on("change", function () {
    const catText = $("#cat_id option:selected").text().trim();
    if (catText === "Requerimiento Arquitectura y Visual") {
  $("#fila_local_cantidad").show();
  $("#select_local, #input_cantidad").prop("disabled", false);
  $("#link_codigos").show();

  // 🔽 Cargar locales dinámicamente desde el backend
  $.post("../../controller/local.php?op=combo", function (data) {
    $("#select_local").html(data);
  }).fail(function (xhr, status, err) {
    console.error("Error al cargar locales:", status, err);
  });

} else {
  $("#fila_local_cantidad").hide();
  $("#select_local, #input_cantidad").prop("disabled", true);
  $("#tick_titulo").prop("readonly", false).val("");
  $("#link_codigos").hide();
}

    actualizarTitulo(); // actualizar en caso de cambio
  });

  // Detecta cambios en los campos para actualizar el título dinámico
  $("#select_local, #input_cantidad, #cats_id").on("change input", function () {
    actualizarTitulo();
  });

  // Función que arma el título dinámico
  function actualizarTitulo() {
    const catText = $("#cat_id option:selected").text().trim();
const subText = $("#cats_id option:selected").text().trim();
    if (catText === "Requerimiento Arquitectura y Visual") {
      const local = $("#select_local option:selected").text().trim() || "";
      const codigo = $("#cats_id option:selected").text().trim() || "";
      const cantidad = $("#input_cantidad").val() || "";

      // Solo si hay al menos un dato, formamos el título
      let titulo = "";
      if (local || codigo || cantidad) {
        titulo = `${local}${local && codigo ? " - " : ""}${codigo}${
          (codigo || local) && cantidad ? " - " : ""
        }${cantidad}`;
      }

      $("#tick_titulo").val(titulo).prop("readonly", true);
    }
else if (subText === "SUBTEST") {

        $("#tick_titulo")
            .val("Solicitud de Insumo")
            .prop("readonly", true);

    }
    else {

        $("#tick_titulo").prop("readonly", false);

    }
  }


  $.post("../../controller/prioridad.php?op=combo", function (data, status) {
    $("#prio_id").html(data);
  });

  // --- Inserta el DIV de advertencia si no existe ya (para que no tengas que tocar HTML)
  if ($("#cai-warning-wrap").length === 0) {
    var warningHtml =
      '<div class="col-lg-12" id="cai-warning-wrap" style="display:none; margin-top:10px;">' +
      '  <div id="cai-warning" class="alert alert-danger" role="alert" style="margin:0;">' +
      '    <strong>⚠️ Atención:</strong> Para la subcategoría <em>CAI</em> de la categoría <em>POS</em> es obligatorio adjuntar al menos un documento.' +
      "  </div>" +
      "</div>";
    // Insertar justo después del contenedor que tiene #cats_id
    var container = $("#cats_id").closest(".col-lg-6");
    if (container.length) container.after(warningHtml);
    else $("#cats_id").parent().after(warningHtml); // fallback
  }

  // Ejecutar reglas iniciales por si ya vienen seleccionados
  applyCaiRules();

  // Escuchadores para aplicar reglas cuando cambian selects
  $("#cat_id").on("change", function () {
    // si subcategorías se recargan vía AJAX, esperar 120ms para que DOM tenga opciones
    setTimeout(applyCaiRules, 120);
  });
  $("#cats_id").on("change", applyCaiRules);
});
//#############################################################

// --- Funciones helper para CAI / POS ---
function selectedTextUpper(selector) {
  var el = $(selector);
  if (!el.length) return "";
  var txt = el.find("option:selected").text() || "";
  return txt.trim().toUpperCase();
}

function isCaiPos() {
  // detecta por texto (POS / CAI) y por value 'CAI' (por si el value fuera 'CAI')
  var catText = selectedTextUpper("#cat_id");
  var subText = selectedTextUpper("#cats_id");
  var subVal = ($("#cats_id").val() || "").toString().trim().toUpperCase();

  return catText === "POS" && (subText === "CAI" || subVal === "CAI");
}

function isSubtest() {
  var catText = selectedTextUpper("#cat_id");
  var subText = selectedTextUpper("#cats_id");

  return catText === "TEST" && subText === "SUBTEST";
}

function applyCaiRules() {
  if (isCaiPos()) {
    $("#cai-warning-wrap").show();
    $("#fileElem").prop("required", true);
  } else {
    $("#cai-warning-wrap").hide();
    $("#fileElem").prop("required", false);
  }

  // TEST / SUBTEST
  if (isSubtest()) {
    $("#subtest-warning-wrap").show();
  } else {
    $("#subtest-warning-wrap").hide();
  }
}

//#############################################################
// guardaryeditar (modificado para chequear archivos cuando corresponde)
function guardaryeditar(e) {
  e.preventDefault(); // evitar envío convencional
  $("#btnguardar").prop("disabled", true);
  $("#btnguardar").html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

  // validaciones base
  if (
    $("#tick_descrip").summernote("isEmpty") ||
    $("#tick_titulo").val() == "" ||
    $("#cats_id").val() == 0 ||
    $("#cat_id").val() == 0 ||
    $("#prio_id").val() == 0
  ) {
    // re-habilitar boton antes de salir
    $("#btnguardar").prop("disabled", false);
    $("#btnguardar").html("Guardar");
    swal("Advertencia!", "Campos Vacíos", "warning");
    return;
  }

  // Si corresponde CAI/POS, validar que haya al menos un archivo
  var filesCount = 0;
  if ($("#fileElem").length && $("#fileElem")[0].files) {
    filesCount = $("#fileElem")[0].files.length;
  }

  if (isCaiPos() && filesCount === 0) {
    // re-habilitar boton antes de salir
    $("#btnguardar").prop("disabled", false);
    $("#btnguardar").html("Guardar");
    if (typeof swal === "function") {
      swal(
        "Atención",
        "Para la subcategoría CAI (POS) es obligatorio adjuntar al menos un documento.",
        "warning"
      );
    } else {
      alert(
        "Para la subcategoría CAI (POS) es obligatorio adjuntar al menos un documento."
      );
    }
    return;
  }

  // construir FormData y anexar archivos (uso correcto files.length)
  var formData = new FormData($("#ticket_form")[0]);
  if ($("#fileElem").length && $("#fileElem")[0].files) {
    var totalfiles = $("#fileElem")[0].files.length;
    for (var i = 0; i < totalfiles; i++) {
      formData.append("files[]", $("#fileElem")[0].files[i]);
    }
  }

  // AJAX de envío
  $.ajax({
    url: "../../controller/ticket.php?op=insert",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (data) {
      try {
        data = JSON.parse(data);
      } catch (err) {
        console.error("Respuesta no JSON:", data);
      }
      // Si todo OK
      $("#tick_titulo").val("");
      $("#tick_descrip").summernote("reset");
      $("#fileElem").val(""); // limpiar input files
      $("#btnguardar").prop("disabled", false);
      $("#btnguardar").html("Guardar");

      if (Array.isArray(data) && data[0] && data[0].tick_id) {
        swal(
          "Correcto!",
          "Ticket Registrado Correctamente: Nro-" + data[0].tick_id,
          "success"
        );
      } else {
        // Mensaje genérico si el controller no devuelve el JSON esperado
        swal("Correcto!", "Ticket Registrado Correctamente", "success");
      }
    },
    error: function (xhr, status, err) {
      console.error("Error AJAX:", err);
      $("#btnguardar").prop("disabled", false);
      $("#btnguardar").html("Guardar");
      swal("Error", "Ocurrió un error al guardar el ticket.", "error");
    },
  });
}

init();