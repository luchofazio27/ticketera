function init(){  
}

$(document).ready(function () {
  $("#tick_descrip").summernote({
    height: 150,
  });

  //esta línea de código es enviar una solicitud POST al archivo categoria.php con el parámetro op=combo, y luego ejecutará una función cuando reciba una respuesta del servidor.
  //Dentro de la función de callback, puedes manipular el contenido recibido a través del parámetro data o manejar el estado de la solicitud con el parámetro status.
  $.post("../../controller/categoria.php?op=combo", function (data, status) {
    $("#cat_id").html(data); //$('#cat_id'): Esto es un selector de jQuery que busca un elemento en el HTML con el ID cat_id
    //.html(data): Este método de jQuery reemplaza el contenido HTML de ese elemento con el valor de data, que es la respuesta que el servidor envió
    //Esta línea realiza una solicitud POST al servidor, recupera datos (probablemente HTML) y luego inserta esos datos en un elemento de la página con el ID cat_id
  });
});

$(document).on("click", "#btnsistemas", function () {
  if ($("#rol_id").val() == 1) {
    $("#lbltitulo").html("Acceso Sistemas");
    $("#btnsistemas").html("Acceso Usuario");
    $("#rol_id").val(2);
    $("#imgtipo").attr("src","public/2.jpg");
  } else {
    $("#lbltitulo").html("Acceso Usuario");
    $("#btnsistemas").html("Acceso Sistemas");
    $("#rol_id").val(1);
    $("#imgtipo").attr("src","public/1.jpg");
  }
});

init();
