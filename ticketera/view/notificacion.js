$(document).ready(function () {
    mostrar_notificacion();
});

function mostrar_notificacion(){

    var formData = new FormData(); //se crea un nuevo objeto FormData, que se utiliza para construir un conjunto de pares clave/valor representando los campos del formulario y sus valores.
        formData.append('usu_id', $('#user_idx').val()); //se agrega el valor de usu_id al objeto formData con la clave 'usu_id'

        $.ajax({
            url: "../../controller/notificacion.php?op=mostrar", //se especifica la URL a la que se enviará la solicitud AJAX
            type: "POST", //se especifica el método HTTP que se utilizará para la solicitud (en este caso, POST)
            data: formData, //se pasan los datos del formulario al objeto formData
            contentType: false, //se establece en false para que jQuery no intente establecer el tipo de contenido de la solicitud
            processData: false, //se establece en false para que jQuery no procese los datos antes de enviarlos
            success: function (data) { //se define una función de éxito que se ejecutará cuando la solicitud AJAX se complete con éxito
                if(data == '') { //si la respuesta del servidor es un array vacío, no se hace nada
                } else {
                data = JSON.parse(data); //se convierte la respuesta del servidor en un objeto JavaScript
                $.notify({
                    icon: 'glyphicon glyphicon-star',
                    message: data.not_mensaje,//se muestra una notificación con el mensaje recibido desde el servidor
                    //url: "http://localhost/TICKETERA/view/DetalleTicket/?ID="+data.tick_id //se establece la URL a la que se redirigirá al hacer clic en la notificación
                });

                 $.post("../../controller/notificacion.php?op=actualizar", {not_id : data.not_id}, function (data) {
                 });
            }
        }
        });
        
}

setInterval(function() {
    mostrar_notificacion();
}, 5000); // Llama a la función cada 5 segundos