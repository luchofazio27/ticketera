

$(document).on("click", "#btnenviar", function(){
    var usu_correo = $("#usu_correo").val(); // Capturamos el valor del input
     if(usu_correo.length == 0){
        swal("Error!", "Campo Vacío", "error");
    } else {
        $.post("../../controller/usuario.php?op=correo", {usu_correo:usu_correo}, function (data) {
        console.log(data);
        if (data == "Existe") {
            $.post("../../controller/email.php?op=recuperar_contra", {usu_correo : usu_correo}, function (data) {
                    console.log(data);
                });
            swal("Correcto!", "Se le ha enviado un correo electronico", "success");
        } else {
            swal("Error!", "El correo no existe", "error");}
            });
    }
    
})