function init(){

}

$(document).ready(function(){
    var tick_id = getUrlParameter('ID'); //Cuando el document se ejecute, vamos a capturar el parametro ID de la URL

    listardetalle(tick_id);

    $('#tickd_descrip').summernote({
        height: 400,
        lang: "es-ES",
            popover: {
        image: [],
        link: [],
        air: []
    },
    callbacks: {
        onImageUpload: function(image) {
            console.log("Image detect...");
            myimagetreat(image[0]);
        },
        onPaste: function (e) {
            console.log("Text detect...");
        }
    },
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough', 'superscript', 'subscript']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']]
    ]
    });

    $('#tickd_descripusu').summernote({
        height: 400,
        lang: "es-ES"
    });

    $('#tickd_descripusu').summernote('disable');
});

var getUrlParameter = function getUrlParameter(sParam) { // Esta es la función que se encarga de extraer el valor del parámetro de la URL
    var sPageURL = decodeURIComponent(window.location.search.substring(1)), //Esta línea obtiene la parte de la URL que sigue al signo de interrogación. decodeURIComponent se utiliza para decodificar cualquier carácter especial que pueda estar presente
    sURLVariables = sPageURL.split('&'), //Esta línea divide la cadena de parámetros en un array, utilizando el carácter & como separador. Cada elemento del array representa un par clave-valor.
    sParameterName,
    i;

    for (i = 0; i < sURLVariables.length; i++) { //Se inicia un bucle que itera sobre cada uno de los pares clave-valor en el array
        sParameterName = sURLVariables[i].split('='); //Dentro del bucle, cada par clave-valor se divide en un array usando el signo = como separador. sParameterName[0] contendrá la clave y sParameterName[1] contendrá el valor.

        if (sParameterName[0] === sParam) { //Aquí se verifica si la clave del parámetro actual es igual al parámetro que se pasó a la función (sParam)
            return sParameterName[1] === undefined ? true : sParameterName[1]; //Si se encuentra el parámetro, se devuelve su valor. Si el valor es undefined, se devuelve true. Esto permite manejar casos donde el parámetro está presente pero no tiene un valor asignado
        }
    }
};

$(document).on("click","#btnenviar", function(){ // Funcion del boton enviar dentro del detalle ticket
    var tick_id = getUrlParameter('ID'); 
    var usu_id = $('#user_idx').val(); //Aquí se está obteniendo el valor de un elemento del DOM con el ID user_idx
    var tickd_descrip = $('#tickd_descrip').val(); //Aquí se está obteniendo el valor de un elemento del DOM con el ID tickd_descrip
    if ( $("#tickd_descrip").summernote("isEmpty")){
        swal("Advertencia!", "Descripción Vacía", "warning");
    } else {
        $.post("../../controller/ticket.php?op=insertdetalle", { tick_id : tick_id, usu_id : usu_id, tickd_descrip : tickd_descrip}, function (data) { //esta línea de código es enviar una solicitud POST al archivo ticket.php con el parámetro "inserdetalle"
            listardetalle(tick_id); // para que ejecute la funcion de listar el detalle del ticket y asi actualizarse automaticamente
            $('#tickd_descrip').summernote('reset'); // Limpia el campo descripcion
            swal("Correcto!", "Registrado Correctamente", "success");
        });
    }
    
})

$(document).on("click","#btncerrarticket", function(){ // Funcion del boton cerrar ticket
    console.log("BOTON CERRAR");
    swal({
        title: "ATENCION!",
        text: "¿Esta seguro de cerrar el Ticket?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-warning",
        confirmButtonText: "Si",
        cancelButtonText: "No",
        closeOnConfirm: false
    },
    function(isConfirm) {
        if (isConfirm) {
            var tick_id = getUrlParameter('ID');
            var usu_id = $('#user_idx').val();
            $.post("../../controller/ticket.php?op=update", { tick_id : tick_id, usu_id : usu_id}, function (data) {    
            });

            listardetalle(tick_id);

            swal({
                title: "Ticket Cerrado",
                text: "Ticket cerrado correctamente",
                type: "success",
                confirmButtonClass: "btn-success"
            });
        }
    });
});

function listardetalle(tick_id){
    $.post("../../controller/ticket.php?op=listardetalle", { tick_id : tick_id}, function (data) { //esta línea de código es enviar una solicitud POST al archivo ticket.php con el parámetro "listardetalle"
        //se le envia un objeto de datos al servidor con la variable tick_id para que identifique el ticket por el cual se consulta
        //la funcion se ejecuta una vez obtenemos la respuesta que se almacenara en el parametro "data"
        $('#lbldetalle').html(data); //.html(data): Este método de jQuery reemplaza el contenido HTML de ese elemento con el valor de data
    });

    $.post("../../controller/ticket.php?op=mostrar", { tick_id : tick_id}, function (data) {
        data = JSON.parse(data); //JSON.parse(data) en JavaScript analiza una cadena de texto en formato JSON y la transforma en un objeto JavaScript. 
        $('#lblestado').html(data.tick_estado);
        $('#lblnomusuario').html(data.usu_nom +' '+data.usu_ape);
        $('#lblfechcrea').html(data.fech_crea);
        $('#lblnomidticket').html("Detalle Ticket "+data.tick_id);
        $('#cat_nom').val(data.cat_nom);
        $('#tick_titulo').val(data.tick_titulo);
        $('#tickd_descripusu').summernote('code', data.tick_descrip);
        if(data.tick_estado_texto == 'Cerrado'){
            $('#pnldetalle').hide();
        }
    });
}

init();