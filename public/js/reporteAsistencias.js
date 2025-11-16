	document.addEventListener("DOMContentLoaded", () => {

		const fechaInicio = document.getElementById("fechaInicio");
		const fechaHasta = document.getElementById("hasta");
		const departamento = document.getElementById("departamento");
		const turno = document.getElementById("turno");
		const agrupar = document.getElementById("agrupar");


		addValidDesdeHasta(fechaInicio,fechaHasta);


		const fecha = new Date();
		// el mes debe obtenerse con los dos digitos
		const mes = `${fecha.getMonth()+1}`.padStart(2, '0');
		const anio = fecha.getFullYear();
		var lastDayOfMonth = new Date(fecha.getFullYear(), fecha.getMonth()+1, 0);
		document.getElementById("fechaInicio").value = `${anio}-${mes}-01`;
		document.getElementById("hasta").value = `${anio}-${mes}-${lastDayOfMonth.getDate()}`;
		const tabla = document.getElementById("reporteAsistencia");

		// si la tabla esta vacia llama a enviar
		if(tabla.rows.length === 0){
			enviar();
		}

		fechaInicio.addEventListener("change", enviar);
		fechaHasta.addEventListener("change", enviar);
		departamento.addEventListener("change", enviar);
		turno.addEventListener("change", enviar);
		agrupar.addEventListener("change", (e)=>{
			
			let sms = e.target.dataset.formtext;
			sms = document.getElementById(sms);
			if(e.target.value === "semana"){
				sms.innerText = "Para este modo se utilizara la fecha de inicio para calcular la semana";
				sms.classList.add("d-block", "text-info");
			}
			else{
				sms.innerText = "";
				sms.classList.remove("d-block", "text-info");
			}
			enviar();
		});


	});
	let control_enviar = false;
	

	async function enviar (){
		if(control_enviar) return;
		// enviar por post
		const fechaInicio = document.getElementById("fechaInicio").value;
		const hasta = document.getElementById("hasta").value;
		const departamento = document.getElementById("departamento").value;
		const turno = document.getElementById("turno").value;
		const agrupar = document.getElementById("agrupar").value;


		const desdeField = document.getElementById("fechaInicio");
		const hastaField = document.getElementById("hasta");
		const departamentoField = document.getElementById("departamento");
		const turnoField = document.getElementById("turno");
		const agruparField = document.getElementById("agrupar");

		let chekc = true;
		[desdeField, hastaField, departamentoField, turnoField, agruparField].forEach((input) => {
			if(input.classList.contains("is-invalid")){
				chekc = false;
			}
		})

		if(!chekc) return;


		resp = await peticion("/Reportes/Asistencia",{
			method: "POST",
			useLoader: "body",
			headers: {
				"Content-Type": "application/json"
			},
			body: JSON.stringify({
				fechaInicio: fechaInicio,
				hasta: hasta,
				departamento: departamento,
				turno: turno,
				agrupar: agrupar,
				action: "consultar",
				
			}),
			before:() => {
				control_enviar = true;
			},
			after:() => {
				control_enviar = false;
			}

		});

		
		


		if(resp = parsearJson(resp)){
			mostrarLoader("body", true);
			if(resp.success){
				// mostrar el reporte


				let table;
				if (DataTable.isDataTable(document.querySelector("#reporteAsistencia"))) {
					table = new DataTable(document.querySelector("#reporteAsistencia"));
					table.destroy();
				}

				// si todo sale bien se optiene un objeto con las propiedades "headers" y "data" que contienen las cabeceras y los datos en forma de arrelgo
				let headers = resp.headers;
				let data = resp.data;
				let tabla = document.getElementById("reporteAsistencia");

				tabla.innerHTML = "";


				let thead = document.createElement("thead");
				let tbody = document.createElement("tbody");


				let tr = document.createElement("tr");

				let semanaDias = ["Lunes","Martes","Miércoles","Jueves","Viernes","Sábado","Domingo"];

				console.log(headers);
				headers.forEach(header => {
					let th = document.createElement("th");
					th.innerHTML = header;
					
					if(agrupar === "semana"){
						if(semanaDias.includes(header.replace(/^(.+)\s.*/,"$1"))){
							th.classList.add("no-sort");
						}
					}else if(agrupar === ''){
						if(header === "descripcion" || header === "tipo"){
							th.classList.add("no-visible");
						}
						else if(header === "Acción"){
							th.classList.add("btn-mostrar-detalle");
						}
						data.forEach(dato => {
							let fecha = dato[3];
							if((fecha = fecha.split("-")).length === 3){
								dato[3] = fecha.reverse().join("/");
							}
						});
						
					}


					tr.appendChild(th);
				})
				thead.appendChild(tr);


				// data.forEach(dato => {
				// 	let tr = document.createElement("tr");
				// 	dato.forEach(dato => {
				// 		let td = document.createElement("td");
				// 		td.innerText = dato;
				// 		tr.appendChild(td);
				// 	})
				// 	tbody.appendChild(tr);
				// });
				
				tabla.appendChild(thead);
				tabla.appendChild(tbody);

				//*************************************
				let drawCallback = function (settings) {};
				if(agrupar == ""){
					data.forEach(dato => {
						let fecha = dato[3];
						if((fecha = fecha.split("-")).length === 3){
							dato[3] = fecha.reverse().join("/");
						}
					});

					drawCallback = function (settings) {
						inicializarTooltips("reporteAsistencia");
					}

				}



				
				
				//document.querySelector("#reporteAsistencia tbody").innerHTML = '';
				
				if (!DataTable.isDataTable(document.querySelector("#reporteAsistencia"))) {
				    table = new DataTable("#reporteAsistencia",{
				            pagingType: 'simple_numbers',
				            language: {
				                url: LOCAL_DIR+'/public/lib/DataTables/datatables-spanish.json'
				            },
				            layout: {
				                bottom1Start: {
				                    pageLength: true
				                },
								bottom1End: {
									buttons: ['excel', 'pdf', 'print']
								}
				            },
				            ordering: true,
							responsive: false,
							data: data,
							initComplete: function () {
								mostrarLoader("body", false);
							},
							columnDefs: [
								{ orderable: false, targets: '.no-sort' }, // Columnas con clase 'no-sort'
								{ 
									visible: false,
									searchable: false,
									targets: '.no-visible'

								},
								{
									targets: '.btn-mostrar-detalle',
									createdCell: function(td, cellData, rowData, row, col) {
										if(rowData[6] != "Asistencia" ){
											let button = crearElemento(
												"div",
												{
													class: "accion pointer text-center",
													type: "button",
													"data-bs-toggle": "tooltip",
													"data-bs-title": "Ver Observación",
													"aria-label": "Ver Observación",
													"data-bs-original-title": "Ver Observación"
												}
											);
	
											button.innerHTML = '<i class="fa-solid fa-eye"></i>';
											button.onclick = () => mostrarDetalles(rowData);
											
											td.appendChild(button);
											
										}
									}
								}
							],
							drawCallbackPlus: drawCallback

				        });
				}
				


				//*************************************




				


				
				//console.log(resp);
			}else{
				mostrarLoader("body", false);
				mostrarError(resp.message);
			}
		}

		











	}

	function mostrarDetalles(row){
		/*
		modalObCedula
		modalObNombre
		modalObFecha
		modalObTurno
		modalObDivision
		modalObObservación
		*/
		const modal = document.getElementById('modalObservacion');
		const modalCedula = document.getElementById('modalObCedula');
		const modalNombre = document.getElementById('modalObNombre');
		const modalFecha = document.getElementById('modalObFecha');
		const modalTurno = document.getElementById('modalObTurno');
		const modalDivision = document.getElementById('modalObDivision');
		const modalObservacion = document.getElementById('modalObObservación');
		const modalTipo = document.getElementById('modalObTipo');
		modalCedula.innerText = row[0];
		modalNombre.innerText = row[1]+" "+row[2];
		modalFecha.innerText = row[3];
		modalTurno.innerText = row[4];
		modalDivision.innerText = row[5];
		modalObservacion.innerText = row[8];
		modalTipo.innerText = row[9];
		//new bootstrap.Modal(document.getElementById('modalObservacion')).show();
		var myModal = new bootstrap.Modal(document.getElementById('modalObservacion'));
		myModal.show();
	}

	function inicializarTooltips(tablaId) {
		// Busca todos los elementos con el atributo de tooltip dentro del cuerpo de la tabla
		document.querySelectorAll(`#${tablaId} [data-bs-toggle="tooltip"]`).forEach(tooltipEl => {
			// Asegúrate de que el tooltip no haya sido ya inicializado para evitar duplicados
			if (!bootstrap.Tooltip.getInstance(tooltipEl)) {
				new bootstrap.Tooltip(tooltipEl);
			}
		});
	}
