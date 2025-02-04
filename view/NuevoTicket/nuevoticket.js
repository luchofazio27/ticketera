//$("#ticket_form"):Este es un selector de jQuery que selecciona el elemento HTML con el ID ticket_form
//La función .on("submit", ...) está escuchando el evento submit del formulario, lo que significa que se ejecutará cuando el usuario intente enviar el formulario
//function(e):La función que se pasa como segundo parámetro a .on("submit", ...) es la que se ejecuta cuando el formulario es enviado
//Cuando el evento submit ocurre, se llama a la función guardaryeditar(e). El evento e se pasa a guardaryeditar, que es responsable de manejar lo que sucede cuando se envía el formulario
function init(){
    $("#ticket_form").on("submit",function(e){
        guardaryeditar(e);
    });
}

//#############################################################

//Es la descripcion de un nuevo ticket
$(document).ready(function() {
    $('#tick_descrip').summernote({
        height: 150
    });

    //esta línea de código es enviar una solicitud POST al archivo categoria.php con el parámetro op=combo, y luego ejecutará una función cuando reciba una respuesta del servidor.
    //Dentro de la función de callback, puedes manipular el contenido recibido a través del parámetro data o manejar el estado de la solicitud con el parámetro status.
    $.post("../../controller/categoria.php?op=combo",function(data, status){
       $('#cat_id').html(data); //$('#cat_id'): Esto es un selector de jQuery que busca un elemento en el HTML con el ID cat_id
       //.html(data): Este método de jQuery reemplaza el contenido HTML de ese elemento con el valor de data, que es la respuesta que el servidor envió
       //Esta línea realiza una solicitud POST al servidor, recupera datos (probablemente HTML) y luego inserta esos datos en un elemento de la página con el ID cat_id
    });
});

//#############################################################

//FormData es una interfaz de JavaScript que facilita la creación de un conjunto de datos para enviar a un servidor usando AJAX.
//$("#ticket_form")[0]: $("#ticket_form") selecciona el formulario con el ID ticket_form, pero como jQuery devuelve un objeto de tipo jQuery, el [0] es para obtener el primer (y único) elemento real del DOM del formulario (es decir, el formulario HTML puro).
function guardaryeditar(e){
    e.preventDefault(); //impide que el formulario se envíe de la manera convencional (es decir, evita la recarga de la página
    var formData = new FormData($("#ticket_form")[0]); //Aquí se está creando un nuevo objeto FormData que contiene todos los datos del formulario ticket_form.
    $.ajax({
        url: "../../controller/ticket.php?op=insert",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            console.log(datos);
        }
    });
}

init();