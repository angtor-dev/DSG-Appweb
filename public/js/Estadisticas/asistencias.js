// Esperar a que el DOM esté cargado
var asistenciasChart = undefined;
var donutAsistenciasChart = undefined;
var donutInasistenciasChart = undefined;
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

        [fechaFinInput, fechaInicioInput].forEach(input => {
            input.setValidStatus();
        });
        
        if (!fechaInicio || !fechaFin) {
            mostrarError('Por favor, ingresa ambas fechas');
            return;
        }
        
        if (new Date(fechaInicio) > new Date(fechaFin)) {
            //mostrarError("La fecha de inicio no puede ser mayor a la fecha fin");
            fechaFinInput.setValidStatus(false, "La fecha de inicio no puede ser mayor a la fecha fin");
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
    


    const div = document.getElementById('asistenciasChart').closest(".card-body.p-4");

    const observer = new ResizeObserver(entries => {
        let height = entries[0].contentRect.height;
        let width = entries[0].contentRect.width;
        const styles = window.getComputedStyle(entries[0].target);
        const paddingLeft = parseFloat(styles.paddingLeft);
        const paddingRight = parseFloat(styles.paddingRight);
        width = width - (paddingLeft + paddingRight);
        if(!asistenciasChart) return;
        if(width<=400) width = 400;
        asistenciasChart.resize(width,400);
    });

    observer.observe(div);
    
    
    
    
    
});



function changeChart(type = 'line'){
    if(!asistenciasChart) return;
    if(type == ''){
        type = asistenciasChart.type;
    }
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
        delete asistenciasChart.data.datasets[0].backgroundColor;
        asistenciasChart.data.datasets[1].borderColor = 'rgb(255, 99, 132)';
        asistenciasChart.data.datasets[1].backgroundColor = '';
        delete asistenciasChart.data.datasets[1].backgroundColor;

        asistenciasChart.data.datasets[1].data = asistenciasChart.data.datasets[1].data.map(x => {
            if(x < 0) return -x;
            return x;
        });

    }

    let data = asistenciasChart.data;



    asistenciasChart.destroy(); // Destruir la gráfica anterior

    createChart(type);

    asistenciasChart.data = data;
    console.log("data", data);
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

        const respuesta = await peticion("/Estadisticas/Asistencias", { 
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
            

            data.lista.forEach(element => {
                labels.push(element[0]);
                inasistencias.push(element[1]);
                asistencias.push(element[2]);
            })


            if(data.lista.length == 0){
                mostrarError("No se encontraron datos");
                document.getElementById("picoAsistencias").closest("div.row").classList.add("d-none");
            }
            else{
                document.getElementById("picoAsistencias").closest("div.row").classList.remove("d-none");


                
                let picoAsistencias = Math.max(...asistencias);
                let picoInasistencias = Math.max(...inasistencias);

                let promedioAsistencias = asistencias.reduce((a, b) => a + b, 0) / asistencias.length;
                let promedioInasistencias = inasistencias.reduce((a, b) => a + b, 0) / inasistencias.length;
                // formatear a dos decimales
                promedioAsistencias = promedioAsistencias.toFixed(2);
                promedioInasistencias = promedioInasistencias.toFixed(2);

                //document.getElementById("promedioAsistencias").innerHTML = promedioAsistencias;
                //document.getElementById("promedioInasistencias").innerHTML = promedioInasistencias;
                
                let labelPicoAsistencias = asistencias.indexOf(picoAsistencias);
                let labelPicoInasistencias = inasistencias.indexOf(picoInasistencias);
                
                let mesPicoAsistencias = labels[labelPicoAsistencias];
                let mesPicoInasistencias = labels[labelPicoInasistencias];
                
                
                document.getElementById("picoAsistencias").innerHTML = picoAsistencias;
                document.getElementById("picoInasistencias").innerHTML = picoInasistencias;
                document.getElementById("mesPicoAsistencias").innerHTML = getMeses(mesPicoAsistencias);
                document.getElementById("mesPicoInasistencias").innerHTML = getMeses(mesPicoInasistencias);
                
                document.getElementById("picoAsistencias").parentNode.parentNode.style.backgroundColor = 'rgb(75, 192, 192)';
                document.getElementById("picoAsistencias").parentNode.parentNode.style.color = '#006363';
                
                document.getElementById("picoInasistencias").parentNode.parentNode.style.backgroundColor = 'rgb(255, 99, 132)';
                document.getElementById("picoInasistencias").parentNode.parentNode.style.color = '#950020';
                let tbody = document.getElementById("promedioDivision");

                tbody.closest("div.row").classList.add("d-none");
                
                if(data.promedio.length>0){
                    // promedio es un arreglo con el promedio de asistencias y inasistencias por division de departamento

                    tbody.innerHTML = "";
                    let totalAsistencias = 0;
                    let totalInasistencias = 0;
                    let datasetDonutAsistencias = [];
                    let datasetDonutInasistencias = [];
                    let labelsDonutAsistencias = [];
                    let labelsDonutInasistencias = [];
                    let listColors = [];
                    let dataAsistencias = {};
                    let dataInasistencias = {};
                    console.log(data.promedio);
                    data.promedio.forEach(element => {
                        tbody.innerHTML += `<tr>
                        <td>${element.division}</td>
                        <td>${element.asistencias}</td>
                        <td>${element.porcentajeAsistencias}%</td>
                        <td>${element.inasistencias}</td>
                        <td>${element.porcentajeInasistencias}%</td>
                        </tr>`;

                        let color1 = getRandomColor(listColors);
                        let color2 = getRandomColor(listColors);

                        labelsDonutAsistencias.push(element.division);
                        labelsDonutInasistencias.push(element.division);

                        dataAsistencias = handlerDonutData(element.division, element.asistencias,"Asistencias", color1, dataAsistencias);
                        dataInasistencias = handlerDonutData(element.division, element.inasistencias,"Inasistencias", color2, dataInasistencias);





                   

                        listColors.push(color1);
                        listColors.push(color1);

                       
                        

                        totalAsistencias += element.asistencias;
                        totalInasistencias += element.inasistencias;

                    });
                    console.log("aqui 2" ,dataAsistencias);
                    donutAsistenciasChart = createDonutChart("donutAsistencias", dataAsistencias, {textCenter:"Asistencias", fontSize:"15px" });

                    donutInasistenciasChart = createDonutChart("donutInasistencias", dataInasistencias, {textCenter:"Inasistencias", fontSize:"15px" });


                    
                    //document.getElementById("promedioAsistencias").innerHTML = totalAsistencias;
                    //document.getElementById("promedioInasistencias").innerHTML = totalInasistencias;
                    tbody.closest("div.row").classList.remove("d-none");

                }

            }





        

            /*
             * se necesita organizar los datos de tal manera que 
             * labels = ["2022-01", "2022-02", "2022-03"]
             * asistencias = [1,2,3]
             * inasistencias = [4,5,6]
             */

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

function handlerDonutData(label, data,labelData, backgroundColor, data_donut= {}){
    if(!data_donut.data){
            data_donut.data = {

                datasets: [
                    {
                        data: [data],
                        backgroundColor: [backgroundColor],
                        label: labelData
                    }
                ],
                labels: [label],
                hoverOffset: 4
            }
    }
    else{
        data_donut.data = {
                datasets: [
                    {
                        data: [...data_donut.data.datasets[0].data, data],
                        backgroundColor: [...data_donut.data.datasets[0].backgroundColor, backgroundColor],
                        hoverOffset: 4,
                        label: labelData
                    }
                ],
                labels: [...data_donut.data.labels, label]
            }

        }
    return data_donut
}

/**
 * @typedef {Object} DonutData
 * @prop {string} [textCenter='Total'] - Texto para mostrar en el centro del gráfico.
 
 * @prop {string} fontSize - default () Tamaño de letra para el texto en el centro.
 * @default '10px'
 * @prop {string} color - Color del texto en el centro ej #ff0000.
 * @default '#000'
 * @prop {string} fontFamily - Fuente del texto en el centro.
 * @default 'Arial'
 * @prop {string} valueFontSize - Tamaño de letra para el valor total en el centro.
 * @default '20px'
 * @prop {string} valueFontFamily - Fuente del valor total en el centro.
 * @default 'Arial'
 * @prop {string} valueColor - Color del valor total en el centro ej #ff0000.
 * @default '#000'
 */

/**
 * Crea un gráfico de donut con Chart.js y agrega texto en el centro
 * del gráfico. Los parámetros son:
 * @param {string} donutID - El ID del elemento HTML que contiene
 * el gráfico.
 * @param {object} data - Un objeto que contiene los datos para
 * el gráfico.
 * @param {DonutData} textCenter - Un objeto que contiene las opciones
 * @return {Chart} - El objeto Chart que se ha creado.
 */
function createDonutChart(donutID, data, textCenter = {}){
    if(donutID == "donutAsistencias" && donutAsistenciasChart){
        donutAsistenciasChart.destroy();
    }
    else if(donutID == "donutInasistencias" && donutInasistenciasChart){
        donutInasistenciasChart.destroy();
    }


    const centerTextPlugin = {
        id: 'centerText',
        afterDraw(chart) {
            if (chart.config.type === 'doughnut' && chart.options.add !== undefined && chart.options.add.textCenter !== undefined) {
                const ctx = chart.ctx;
                const centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                const centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;
                const textCenter = chart.options.add.textCenter;
                const fontSize = chart.options.add.fontSize || '20px';
                const color = chart.options.add.color || '#000';
                const fontFamily = chart.options.add.fontFamily || 'Arial';
                const fontSizeInt = parseInt(fontSize.match(/\d+/), 10);
                let lineHeight = fontSizeInt * 1.2;
                const valueFontSize = chart.options.add.valueFontSize || '16px';
                const valueColor = chart.options.add.valueColor || '#000';
                const valueFontFamily = chart.options.add.valueFontFamily || 'Arial';
                const valueFontSizeInt = parseInt(valueFontSize.match(/\d+/), 10);

                ctx.save(); // Save the current canvas state

                

                ctx.font = `${fontSize} ${fontFamily}`; // Set your desired font
                ctx.fillStyle = color; // Set your desired text color
                ctx.textAlign = 'center'; // Center the text horizontally
                ctx.textBaseline = 'middle'; // Center the text vertically


                // Example text:
                const totalValue = chart.data.datasets[0].data.reduce((sum, value) => sum + value, 0);

                let line1 = `${textCenter}`;
                let line2 = `${totalValue}`;
                
                ctx.fillText(line1, centerX, centerY - lineHeight / 2);

                ctx.font = `${valueFontSize} ${valueFontFamily}`; // Set your desired font
                ctx.fillStyle = valueColor; // Set your desired text color
                lineHeight = valueFontSizeInt * 1.2;


                ctx.fillText(line2, centerX, centerY + lineHeight / 2);

                ctx.restore(); // Restore the canvas state
            }
        }
    };

    // Register the plugin (do this before creating your chart)
    Chart.register(centerTextPlugin);







    data = data.data;
    const options = {
        plugins: {
            annotation: {
                annotations: {
                    dLabel: {
                        type: 'doughnutLabel',
                        content: ({ chart }) => ['Total',
                            chart.getDatasetMeta(0).total,
                            'last 7 months'
                        ],
                        font: [{ size: 60 }, { size: 50 }, { size: 30 }],
                        color: ['black', 'red', 'grey']
                    }
                }
            }
        },
        responsive: true,
        maintainAspectRatio: false

    };
    const defaultTextCenter = {
        textCenter: 'Total',
        fontSize: '10px',
        color: '#757575',
        fontFamily: 'Arial',
        valueFontSize: '20px',
        valueFontFamily: 'Arial',
        valueColor: '#000000'
    };

    let addOption = {...defaultTextCenter, ...textCenter};

    let config = {
        type: 'doughnut',
        data: {
            datasets: [
                {
                    data: [10, 20, 15, 5, 50],
                    backgroundColor: [
                        '#ff6384ff',
                        '#ff9f40ff',
                        '#ffcd56ff',
                        '#4bc0c0ff',
                        '#36a2ebff',
                    ],
                },
            ],
            labels: ['Red', 'Orange', 'Yellow', 'Green', 'Blue'],
            hoverOffset: 4
        },
        options: {
            add: addOption,
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    color: '#fff', // Color of the text (e.g., white for dark bars)
                    anchor: 'center', // Position of the label (start, center, end)
                    align: 'center',  // Alignment relative to the anchor (start, center, end)
                    font: {
                        weight: 'bold', // Make the font bold
                        size: 14        // Set font size
                    },
                    formatter: function (value, context) {
                        // Custom formatting function (e.g., add a currency symbol)
                        return value + ' units';
                    }
                }
            },
        }
    }

    config.data = data;

    let donut = document.getElementById(donutID).getContext('2d');
    let donutChart = new Chart(donut, config);
    return donutChart;
    
}

/**
 * Convierte un string o un arreglo de strings en formato AAAA-MM en un string o arreglo de strings en formato AAAA-Mes
 * @param {string|Array<string>} fechaArreglo
 * @returns {string|Array<string>}
 */
function getMeses(fechaArreglo){
    let meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    let resp = [];
    if(Array.isArray(fechaArreglo)){

        fechaArreglo.forEach(element => {
            let mes = element.split('-')[1].replace('0', '');
            let anio = element.split('-')[0];
            resp.push(anio + "-" + meses[mes]);
        })
    }
    else if(typeof fechaArreglo == 'string'){
        let mes = fechaArreglo.split('-')[1].replace('0', '');
        let anio = fechaArreglo.split('-')[0];
        resp.push(anio + "-" + meses[mes]);
    }
        return resp;
}

/**
 * Generates a random color in hexadecimal format.
 * Ensures the generated color is not in the list of colors already used.
 * @param {Array<string>} coloUsed - Array of colors that have already been used.
 * @returns {string} A new random color in hexadecimal format.
 */

function getRandomColor(coloUsed) {

    const firsts = [
        '#ff6384ff',
        '#ff9f40ff',
        '#ffcd56ff',
        '#4bc0c0ff',
        '#36a2ebff',
    ]

    // Intenta utilizar los colores del arreglo firsts
    for (let color of firsts) {
        if (!coloUsed.includes(color)) {
            return color;
        }
    }

    // Si todos los colores del arreglo firsts están usados, genera un color aleatorio
    let letters = '0123456789ABCDEF';
    let color = '#';
    do {
        color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
    } while (coloUsed.includes(color));
    return color;
}