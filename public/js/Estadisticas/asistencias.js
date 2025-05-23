// Esperar a que el DOM esté cargado
var asistenciasChart = undefined;
document.addEventListener('DOMContentLoaded', function() {
    // Obtener referencias a los elementos
    
    const btnFiltrar = document.getElementById('filtrar-btn');
    const fechaInicioInput = document.getElementById('fechaInicio');
    const fechaFinInput = document.getElementById('hasta');
    const cedulaTrabajador = document.getElementById('cedulaTrabajador');
    const departamento = document.getElementById('departamento');
    
    // Establecer fechas por defecto (últimos 5 meses)
    const fechaFin = new Date();
    const fechaInicio = new Date();

    // fecha inicio sera la fecha fin menos 5 meses
    fechaInicio.setMonth(fechaFin.getMonth() - 5);
    

    
    fechaInicioInput.valueAsDate = fechaInicio;
    fechaFinInput.valueAsDate = fechaFin;

    

    // Manejador del botón filtrar
    btnFiltrar.addEventListener('click', function() {
        const fechaInicio = fechaInicioInput.value;
        const fechaFin = fechaFinInput.value;
        
        if (!fechaInicio || !fechaFin) {
            mostrarError('Por favor, ingresa ambas fechas');
            return;
        }
        
        if (new Date(fechaInicio) > new Date(fechaFin)) {
            mostrarError("La fecha de inicio no puede ser mayor a la fecha fin");
            return;
        }

        if(cedulaTrabajador.value != ""){
            if(!/^[0-9]{7,8}$/.test(cedulaTrabajador.value)){
                mostrarError("La cedula no es valida");
                return;
            }
            else departamento.value = "";
        }

        if(departamento.value != ""){
            if(!/^[0-9]+$/.test(departamento.value)){
                mostrarError("El departamento no es valido");
                return;
            }
            else cedulaTrabajador.value = "";
        }
            
        cargarDatos(fechaInicio, fechaFin, cedulaTrabajador.value, departamento.value);
    });

    createChart();


    
    // Cargar datos iniciales
    cargarDatos(fechaInicio.toISOString().split('T')[0], fechaFin.toISOString().split('T')[0]);
   
    
    
    
    
    
});



function changeChart(type = 'line'){
    const ctx = document.getElementById('asistenciasChart').getContext('2d');
    
    if(type == 'bar'){
        asistenciasChart.data.datasets[0].borderColor = 'rgb(75, 192, 192)';
        asistenciasChart.data.datasets[0].backgroundColor = 'rgb(75, 192, 192)';
        asistenciasChart.data.datasets[1].borderColor = 'rgb(255, 99, 132)';
        asistenciasChart.data.datasets[1].backgroundColor = 'rgb(255, 99, 132)';

        // pasa a negativo las inasistencias 
        asistenciasChart.data.datasets[1].data = asistenciasChart.data.datasets[1].data.map(x => {
            if(x > 0) return -x;
            return x;
        });

        

    }
    else if(type == 'line'){
        asistenciasChart.data.datasets[0].borderColor = 'rgb(75, 192, 192)';
        asistenciasChart.data.datasets[0].backgroundColor = '';
        asistenciasChart.data.datasets[1].borderColor = 'rgb(255, 99, 132)';
        asistenciasChart.data.datasets[1].backgroundColor = '';

        asistenciasChart.data.datasets[1].data = asistenciasChart.data.datasets[1].data.map(x => {
            if(x < 0) return -x;
            return x;
        });

    }

    let data = asistenciasChart.data;



    asistenciasChart.destroy(); // Destruir la gráfica anterior

    createChart(type);

    asistenciasChart.data = data;
    asistenciasChart.update();

}
 function createChart(type = 'line'){
        const ctx = document.getElementById('asistenciasChart').getContext('2d');
        if(asistenciasChart){
            asistenciasChart.destroy();
        }
        // Crear la gráfica inicial
             asistenciasChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Asistencias',
                            data: [],
                            borderColor: 'rgb(75, 192, 192)',

                        },
                        {
                            label: 'Inasistencias',
                            data: [],
                            borderColor: 'rgb(255, 99, 132)',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            //beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha'
                            }
                        }
                    }
                }
            });
        

    }

// Función para cargar datos
async function cargarDatos(fechaInicio, fechaFin, cedulaTrabajador ="", departamento = "") {
        // Aquí deberías hacer una llamada a tu API o backend
        // Ejemplo con fetch:
    if(cedulaTrabajador !='') departamento = "";

        const respuesta = await peticion("/EstadisticasAsistencias", { 
            method: "POST",
            body: JSON.stringify({ 
                fechaIn: fechaInicio,
                 fechaOut: fechaFin
                 ,idTrabajador: cedulaTrabajador
                 ,idDepartamento: departamento
                 }),
            useLoader:'body'
        });

        const data = parsearJson(respuesta);

        

        let labels = [];
        let asistencias = [];
        let inasistencias = [];
        
        if(data.success) {
            if(data.lista.length == 0){
                mostrarError("No se encontraron datos");
                //return;
            }
            data.lista.forEach(element => {
                labels.push(element[0]);
                inasistencias.push(element[1]);
                asistencias.push(element[2]);
            })

            /*
             * se necesita organizar los datos de tal manera que 
             * labels = ["2022-01", "2022-02", "2022-03"]
             * asistencias = [1,2,3]
             * inasistencias = [4,5,6]
             */

            console.log(labels);
            console.log(asistencias);
            console.log(inasistencias);
            // Actualizar la gráfica
            if(asistenciasChart.config.type == 'bar'){ 
                inasistencias = inasistencias.map(x=> -x);

            }
            asistenciasChart.data.labels = getMeses(labels);
            asistenciasChart.data.datasets[0].data = asistencias;
            asistenciasChart.data.datasets[1].data = inasistencias;
            asistenciasChart.update();

        }
        else {
            mostrarError(data.message);

            
        }
}

function getMeses(fechaArreglo){
    let meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    let resp = [];
    fechaArreglo.forEach(element => {
        let mes = element.split('-')[1].replace('0', '');
        let anio = element.split('-')[0];
        resp.push(anio + "-" + meses[mes]);
    })
    return resp;
}