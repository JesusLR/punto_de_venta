var myChart = null;  // Variable global para almacenar el gráfico
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-Token": $("meta[name=csrf-token]").attr("content"),
        },
    });

    inputFechas()
    estadistica()
});

// function fechaInicio(){
//  // Obtener la fecha de hoy
//  var today = new Date();

//  // Restar un día para obtener la fecha de ayer
//  today.setDate(today.getDate() - 7);

//  // Formatear la fecha en el formato YYYY-MM-DD
//  var dd = String(today.getDate()).padStart(2, '0');
//  var mm = String(today.getMonth() + 1).padStart(2, '0'); // Enero es 0
//  var yyyy = today.getFullYear();

//  today = yyyy + '-' + mm + '-' + dd; // Formato de fecha: YYYY-MM-DD

//  // Asignar la fecha de ayer al campo de fecha usando jQuery
//  $('#dtFechaInicio').val(today);
// }

function inputFechas(){
 // Obtener la fecha de hoy
 var today = new Date();
 var tomorrow = new Date();

 // Restar un día para obtener la fecha de ayer
 today.setDate(today.getDate());
 tomorrow.setDate(today.getDate() + 1);

 // Formatear la fecha en el formato YYYY-MM-DD
 var dd = String(today.getDate()).padStart(2, '0');
 var ddTomorrow = String(tomorrow.getDate()).padStart(2, '0');
 var mm = String(today.getMonth() + 1).padStart(2, '0'); // Enero es 0
 var yyyy = today.getFullYear();

 today = yyyy + '-' + mm + '-' + dd; // Formato de fecha: YYYY-MM-DD
 tomorrow = yyyy + '-' + mm + '-' + ddTomorrow; // Formato de fecha: YYYY-MM-DD

 // Asignar la fecha de ayer al campo de fecha usando jQuery
 $('#dtFechaInicio').val(today);
 $('#dtFechaFinal').val(tomorrow);
}

function estadistica() {
    $.ajax({
        url: "/graficaVentas",
        type: "post",
        dataType: "json",
        data: {
            dtFechaInicio: $("#dtFechaInicio").val(),
            dtFechaFinal: $("#dtFechaFinal").val(),
        },
        success: function (data) {
            if (data.lSuccess) {

                // Preparar los datos para la gráfica
                var labels = [];
                var dataValues = [];

                // Recorrer los productos y extraer los datos necesarios
                data.productos.forEach(function (producto) {
                    labels.push(producto.codigo_barras);  // Puedes usar código de barras o nombres de productos si tienes más datos
                    dataValues.push(producto.total_cantidad);
                });

                // Obtener el contexto del canvas
                var ctx = document.getElementById('myChart').getContext('2d');

                // Si ya existe un gráfico, destrúyelo antes de crear uno nuevo
                if (myChart != null) {
                    myChart.destroy();
                }

                // Crear el gráfico
                myChart = new Chart(ctx, {
                    type: 'bar',  // Tipo de gráfico: barra
                    data: {
                        labels: labels,  // Etiquetas de los productos (códigos de barras)
                        datasets: [{
                            label: 'Productos más vendidos',
                            data: dataValues,  // Datos de la cantidad vendida
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',  // Color de fondo de las barras
                            borderColor: 'rgba(54, 162, 235, 1)',  // Color del borde de las barras
                            borderWidth: 1  // Ancho del borde de las barras
                        }]
                    },
                    options: {
                        responsive: true,  // Hacer que el gráfico sea responsivo
                        scales: {
                            y: {
                                beginAtZero: true  // Comenzar la escala en cero
                            }
                        }
                    }
                });

            } else {
                if (myChart != null) {
                    myChart.destroy();
                }
                swal.fire({
                    title: "Error",
                    text: data.cMensaje,
                    icon: "error",
                    showConfirmButton: true,
                    confirmButtonClass: "btn btn-success btn-round",
                    confirmButtonText: "Aceptar",
                });
            }
        },
        error: function (data) {
            if (myChart != null) {
                myChart.destroy();
            }
            swal.fire({
                title: "Error",
                text: data.cMensaje,
                icon: "error",
                showConfirmButton: true,
                confirmButtonClass: "btn btn-success btn-round",
                confirmButtonText: "Aceptar",
            });
        },
    });
}

$("#btnBuscarEstadistica").on('click', function () {
    estadistica()
});
