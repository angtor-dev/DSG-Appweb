	document.addEventListener("DOMContentLoaded", () => {

		const fechaInicio = document.getElementById("fechaInicio");
		const fechaHasta = document.getElementById("hasta");
		const departamento = document.getElementById("departamento");
		const turno = document.getElementById("turno");
        const cargo = document.getElementById("cargo");
		const agrupar = document.getElementById("agrupar");
		const activo = document.getElementById("activo");


		const fecha = new Date();
		// el mes debe obtenerse con los dos digitos
		const mes = `${fecha.getMonth()+1}`.padStart(2, '0');
		const anio = fecha.getFullYear();
		var lastDayOfMonth = new Date(fecha.getFullYear(), fecha.getMonth()+1, 0);
		// document.getElementById("fechaInicio").value = `${anio}-${mes}-01`;
		// document.getElementById("hasta").value = `${anio}-${mes}-${lastDayOfMonth.getDate()}`;
		const tabla = document.getElementById("reporteAsistencia");

		// si la tabla esta vacia llama a enviar
		if(tabla.rows.length === 0){
			enviar();
		}

		fechaInicio.addEventListener("change", enviar);
		fechaHasta.addEventListener("change", enviar);
		departamento.addEventListener("change", enviar);
		turno.addEventListener("change", enviar);
		cargo.addEventListener("change", enviar);
		agrupar.addEventListener("change", enviar);
		activo.addEventListener("change", enviar);


	});
	let control_enviar = false;
	

	async function enviar (){
		if(control_enviar) return;
		// enviar por post
		const fechaInicio = document.getElementById("fechaInicio").value;
		const hasta = document.getElementById("hasta").value;
		const departamento = document.getElementById("departamento").value;
		const turno = document.getElementById("turno").value;
        const cargo = document.getElementById("cargo").value;
		const activo = document.getElementById("activo").checked ? 1 : 0;
		const agrupar = document.getElementById("agrupar").value;

		resp = await peticion("/Reportes/Trabajadores",{
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
                cargo: cargo,
				activo: activo,
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

				console.log(headers);
				headers.forEach(header => {
					let th = document.createElement("th");
					th.innerText = header;
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
							responsive: true,
							data: data,
							initComplete: function () {
								mostrarLoader("body", false);
							}
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
