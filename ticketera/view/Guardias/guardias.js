$(document).ready(function () {

    cargarGuardias();

});

function cargarGuardias() {

    $.getJSON(
        "../../controller/guardias.php?op=mostrar",
        function(data){

            let html = "";
            let fecha = new Date(data[0].fecha_modificacion);

let fechaFormateada =
    fecha.toLocaleDateString('es-AR') +
    ' ' +
    fecha.toLocaleTimeString('es-AR', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    }) +
    ' hs';

            if(data.length > 0){

                html += `
    <div class="alert alert-info">

        <h4>👨‍💻 Personal de Guardia para este <b>FIN DE SEMANA</b></h4>

        <p style="margin-bottom:15px;">
            <small>
                Última actualización:
                ${fechaFormateada}
            </small>
        </p>

        <hr>
`;

                html += `
                    <p>
                        <strong>${data[0].guardia1_nombre} ${data[0].guardia1_apellido}</strong><br>
                        📞 ${data[0].celular_guardia1}
                    </p>
                `;

                html += `
                    <p>
                        <strong>${data[0].guardia2_nombre} ${data[0].guardia2_apellido}</strong><br>
                        📞 ${data[0].celular_guardia2}
                    </p>
                `;

                html += `
                    </div>
                `;
            }

            $("#guardias_container").html(html);

        }
    );

}