$(document).ready(function () {

    cargarUsuarios();

    setTimeout(function(){
        cargarGuardiaActual();
    },500);

    $("#btnGuardar").click(function () {

    console.log("Guardando guardias...");

    $.post(
        "../../controller/guardias.php?op=guardar",
        {
            guardia1: $("#guardia1").val(),
            guardia2: $("#guardia2").val(),
            celular1: $("#celular1").val(),
            celular2: $("#celular2").val()
        },
        function(response){

            console.log(response);

            swal(
                "Correcto",
                "Guardias actualizadas",
                "success"
            );

        }
    );

});

});

function cargarUsuarios() {

    $.getJSON(
        "../../controller/guardias.php?op=combo_usuarios",
        function(data){

            console.log(data);

            $("#guardia1").html("");
            $("#guardia2").html("");

            $.each(data, function(i, item){

    $("#guardia1").append(
        '<option value="' + item.usu_id + '">' +
        item.nombre +
        '</option>'
    );

    $("#guardia2").append(
        '<option value="' + item.usu_id + '">' +
        item.nombre +
        '</option>'
    );

});

        }
    );

}

function cargarGuardiaActual() {

    $.getJSON(
        "../../controller/guardias.php?op=guardia_actual",
        function(data){

            console.log("Guardia actual:", data);

            if(data.length > 0){

                $("#guardia1").val(data[0].guardia1_id);
                $("#guardia2").val(data[0].guardia2_id);

                $("#celular1").val(data[0].celular_guardia1);
                $("#celular2").val(data[0].celular_guardia2);
            }
        }
    );
}