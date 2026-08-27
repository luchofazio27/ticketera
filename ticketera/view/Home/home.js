function init() {}

$(document).ready(function () {

    // Traer totales dinámicos según rol
    $.post("../../controller/ticket.php?op=total", function (data) {
        data = JSON.parse(data);
        $("#lbltotal").html(data.TOTAL);
    });

    $.post("../../controller/ticket.php?op=totalabierto", function (data) {
        data = JSON.parse(data);
        $("#lbltotalabiertos").html(data.TOTAL);
    });

    $.post("../../controller/ticket.php?op=totalcerrado", function (data) {
        data = JSON.parse(data);
        $("#lbltotalcerrados").html(data.TOTAL);
    });

    // Listar últimos tickets
    listarUltimosTickets();

});

// ---------------------------------------------
// FUNCION PARA MOSTRAR LOS ÚLTIMOS TICKETS
// ---------------------------------------------
function listarUltimosTickets() {
    $.ajax({
        url: "../../controller/ticket.php?op=listar_ultimos_x_sector", // o listar_ultimos_x_usuario según rol
        type: "POST",
        success: function (data) {
            $("#ultimosTicketsBody").html(data);
        }
    });
}


$(document).ready(function () {
  var usu_id = $("#user_idx").val(); //Aquí se está obteniendo el valor de un elemento del DOM con el ID user_idx
  var rol_id = $("#rol_idx").val();

  if ($("#rol_idx").val() == 1) {
    //Validamos que Rol ID es, 1 o 2.
    $.post(
      "../../controller/usuario.php?op=total",
      { usu_id: usu_id },
      function (data) {
        data = JSON.parse(data);
        $("#lbltotal").html(data.TOTAL);
      }
    );

    $.post(
      "../../controller/usuario.php?op=totalabierto",
      { usu_id: usu_id },
      function (data) {
        data = JSON.parse(data);
        $("#lbltotalabiertos").html(data.TOTAL);
      }
    );

    $.post(
      "../../controller/usuario.php?op=totalcerrado",
      { usu_id: usu_id },
      function (data) {
        data = JSON.parse(data);
        $("#lbltotalcerrados").html(data.TOTAL);
      }
    );

    $.post(
      "../../controller/usuario.php?op=grafico",
      { usu_id: usu_id },
      function (data) {
        data = JSON.parse(data);

        new Morris.Bar({
          element: "divgrafico",
          data: data,
          xkey: "nom",
          ykeys: ["total"],
          labels: ["Value"],
          barColors: ["#1AB244"],
        });
      });

      $("#idcalendar").fullCalendar({
        lang: "es",
        header: {
          left: "prev,next today",
          center: "title",
          right: "month,basicWeek,basicDay",
        },
        defaultView: "month",
        events: {
          url: "../../controller/ticket.php?op=usu_calendar",
          method: "POST",
          data: { usu_id: usu_id }
        },
      });
      

  } else if (rol_id == 3) {
        // supervisora
        $.post("../../controller/ticket.php?op=total_supervisora", function (data) {
            data = JSON.parse(data);
            $("#lbltotal").html(data.TOTAL);
        });

        $.post("../../controller/ticket.php?op=totalabierto_supervisora", function (data) {
            data = JSON.parse(data);
            $("#lbltotalabiertos").html(data.TOTAL);
        });

        $.post("../../controller/ticket.php?op=totalcerrado_supervisora", function (data) {
            data = JSON.parse(data);
            $("#lbltotalcerrados").html(data.TOTAL);
        });

        $.post("../../controller/ticket.php?op=grafico_supervisora", function (data) {
            data = JSON.parse(data);
            new Morris.Bar({
                element: "divgrafico",
                data: data,
                xkey: "nom",
                ykeys: ["total"],
                labels: ["Value"],
                barColors: ["#1AB244"],
            });
        });

        $("#idcalendar").fullCalendar({
            lang: "es",
            header: {
                left: "prev,next today",
                center: "title",
                right: "month,basicWeek,basicDay",
            },
            defaultView: "month",
            events: {
                url: "../../controller/ticket.php?op=usu_calendar_supervisora",
                method: "POST",
            },
        });
  
      } else {
    $.post("../../controller/ticket.php?op=total", function (data) {
      data = JSON.parse(data);
      $("#lbltotal").html(data.TOTAL);
    });

    $.post("../../controller/ticket.php?op=totalabierto", function (data) {
      data = JSON.parse(data);
      $("#lbltotalabiertos").html(data.TOTAL);
    });

    $.post("../../controller/ticket.php?op=totalcerrado", function (data) {
      data = JSON.parse(data);
      $("#lbltotalcerrados").html(data.TOTAL);
    });

    $.post("../../controller/ticket.php?op=grafico", function (data) {
      data = JSON.parse(data);

      new Morris.Bar({
        element: "divgrafico",
        data: data,
        xkey: "nom",
        ykeys: ["total"],
        labels: ["Value"],
      });

      $("#idcalendar").fullCalendar({
        lang: "es",
        header: {
          left: "prev,next today",
          center: "title",
          right: "month,basicWeek,basicDay",
        },
        defaultView: "month",
        events: {
          url: "../../controller/ticket.php?op=all_calendar",
        },
      });

    });
  }
});

init();
