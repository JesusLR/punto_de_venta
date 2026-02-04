var myChart = null;  // Variable global para almacenar el gráfico
var datosActuales = null; // Variable para guardar los datos actuales

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-Token": $("meta[name=csrf-token]").attr("content"),
        },
    });

    inputFechas();
    estadistica();
    
    // Eventos
    $("#btnBuscarEstadistica").on('click', function () {
        console.log('Botón buscar presionado');
        estadistica();
    });

    $(document).on('change', '#cTipoGrafica', function () {
        console.log('Tipo de gráfica cambiado a:', $(this).val());
        if (datosActuales) {
            dibujarGrafico(datosActuales);
        }
    });

    $(document).on('change', '#cLimiteRegistros', function () {
        console.log('Límite cambiado a:', $(this).val());
        estadistica();
    });
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
            limite: $("#cLimiteRegistros").val() || 10,
        },
        success: function (data) {
            if (data.lSuccess) {
                // Preparar los datos para la gráfica
                var labels = [];
                var dataValues = [];

                // Recorrer los productos y extraer los datos necesarios
                data.productos.forEach(function (producto) {
                    labels.push(producto.codigo_barras);
                    dataValues.push(producto.total_cantidad);
                });

                // Guardar los datos actuales
                datosActuales = {
                    labels: labels,
                    dataValues: dataValues
                };

                // Dibujar el gráfico
                dibujarGrafico(datosActuales);
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

function dibujarGrafico(datos) {
    var labels = datos.labels;
    var dataValues = datos.dataValues;
    
    // Obtener el tipo de gráfico seleccionado
    var tipoGrafica = $("#cTipoGrafica").val() || 'bar';
    
    console.log('Dibujando gráfico tipo:', tipoGrafica);
    console.log('Datos:', dataValues);

    // Obtener el contexto del canvas
    var ctx = document.getElementById('myChart').getContext('2d');

    // Si ya existe un gráfico, destrúyelo
    if (myChart != null) {
        myChart.destroy();
    }

    // Determinar si usar eje indexAxis
    var usarIndexAxis = (tipoGrafica === 'bar' && dataValues.length > 5);

    // Colores para el dataset
    var coloresDataset = generarColores(dataValues.length, tipoGrafica);
    
    // Configurar el color de borde y ancho según tipo
    var borderColorValue = tipoGrafica === 'line' ? '#D4AF37' : '#2a2a2a';
    var borderWidthValue = tipoGrafica === 'line' ? 3 : 2;
    var backgroundColor = tipoGrafica === 'line' 
        ? 'rgba(212, 175, 55, 0.1)' 
        : coloresDataset;

    // Crear el dataset
    var dataset = {
        label: 'Productos Vendidos',
        data: dataValues,
        borderColor: borderColorValue,
        borderWidth: borderWidthValue,
        hoverBackgroundColor: '#1a1a1a',
        hoverBorderColor: '#D4AF37',
        hoverBorderWidth: tipoGrafica === 'line' ? 4 : 3,
    };

    if (tipoGrafica === 'bar') {
        dataset.backgroundColor = coloresDataset;
        dataset.borderRadius = 6;
    } else if (tipoGrafica === 'line') {
        dataset.backgroundColor = backgroundColor;
        dataset.tension = 0.4;
        dataset.fill = true;
        dataset.pointBackgroundColor = '#D4AF37';
        dataset.pointBorderColor = '#2a2a2a';
        dataset.pointBorderWidth = 2;
        dataset.pointRadius = 5;
        dataset.pointHoverRadius = 7;
    } else if (tipoGrafica === 'pie' || tipoGrafica === 'doughnut') {
        dataset.backgroundColor = coloresDataset;
        dataset.borderColor = 'white';
        dataset.borderWidth = 2;
    }

    // Configurar escalas
    var scales = {};
    if (tipoGrafica !== 'pie' && tipoGrafica !== 'doughnut') {
        var axisX = {
            beginAtZero: true,
            grid: {
                color: 'rgba(212, 175, 55, 0.1)',
                drawBorder: true,
                borderColor: '#e0e0e0'
            },
            ticks: {
                color: '#2a2a2a',
                font: {
                    size: 11,
                    weight: '500'
                }
            }
        };

        var axisY = {
            beginAtZero: true,
            grid: {
                color: 'rgba(212, 175, 55, 0.1)',
                drawBorder: true,
                borderColor: '#e0e0e0'
            },
            ticks: {
                color: '#2a2a2a',
                font: {
                    size: 11,
                    weight: '500'
                }
            }
        };

        if (usarIndexAxis) {
            scales.x = axisY;
            scales.y = axisX;
        } else {
            scales.x = axisX;
            scales.y = axisY;
        }
    }

    // Crear el gráfico
    myChart = new Chart(ctx, {
        type: tipoGrafica,
        data: {
            labels: labels,
            datasets: [dataset]
        },
        options: {
            indexAxis: usarIndexAxis ? 'y' : 'x',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: '#2a2a2a',
                        font: {
                            size: 12,
                            weight: '600',
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                        },
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(42, 42, 42, 0.9)',
                    borderColor: '#D4AF37',
                    borderWidth: 2,
                    titleColor: '#D4AF37',
                    bodyColor: '#ffffff',
                    padding: 12,
                    displayColors: false,
                    titleFont: {
                        size: 13,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    },
                    callbacks: {
                        label: function(context) {
                            if (tipoGrafica === 'pie' || tipoGrafica === 'doughnut') {
                                return 'Cantidad: ' + context.parsed + ' unidades';
                            }
                            return 'Cantidad: ' + context.parsed.y + ' unidades';
                        }
                    }
                }
            },
            scales: scales
        }
    });
}

function generarColores(cantidad, tipo) {
    var colores = ['#D4AF37', '#C9A927', '#E5C158', '#D4AF37', '#C9A927', '#E5C158'];
    
    if (tipo === 'pie' || tipo === 'doughnut') {
        // Para gráficos circulares, usar colores más variados
        var coloresPie = [
            '#D4AF37', '#C9A927', '#E5C158', 
            '#2a2a2a', '#1a1a1a', '#45B7D1',
            '#FF6B6B', '#4ECDC4', '#95E1D3'
        ];
        return coloresPie.slice(0, cantidad);
    }
    
    return colores.slice(0, cantidad).length > 0 
        ? colores.slice(0, cantidad)
        : Array(cantidad).fill('#D4AF37');
}
