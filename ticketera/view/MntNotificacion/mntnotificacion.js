$(document).ready(function() {
    var usu_id = $("#user_idx").val();

    tabla = $("#notificacion_data").DataTable({
        aProcessing: true,
        aServerSide: true,
        dom: "Bfrtip",
        searching: true,
        lengthChange: false,
        colReorder: true,
        buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
        ajax: {
            url: "../../controller/notificacion.php?op=listar",
            type: "post",
            dataType: "json",
            data: { usu_id: usu_id },
            error: function(e) {
                console.log(e.responseText);
            }
        },
        bDestroy: true,
        responsive: true,
        bInfo: true,
        iDisplayLength: 10,
        autoWidth: false,
        ordering: false, // Orden lo maneja SQL
        columnDefs: [
            { targets: 1, className: "text-center", orderable: false, searchable: false }
        ],
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando un total de _TOTAL_ registros",
            sInfoEmpty: "Mostrando un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sSearch: "Buscar:",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            }
        }
    });
});

$(document).on("click", ".btn-inline", function() {
    const ciphertext = $(this).data("ciphertext");
    window.location.href = "https://ticketsver.online/ticketera/view/DetalleTicket/?ID=" + ciphertext;
});