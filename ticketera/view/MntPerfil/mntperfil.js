$(document).on("click", "#btnguardar", function(){
    var pass = $("#txtpass").val(); // Capturamos el valos del input
    var newpass = $("#txtpassnew").val();

    if(pass.length == 0 || newpass.length == 0){
        swal("Error!", "Campos Vacios", "error");
    } else {
        if (pass == newpass){
            var usu_id = $('#user_idx').val();
            $.post("../../controller/usuario.php?op=password", {usu_id:usu_id,usu_pass:newpass}, function (data) {
                swal("Correcto!", "Actualizado Correctamente", "success");
            });
        } else {
            swal("Error!", "Las contraseñas NO coinciden", "error");
        }
    }
  });