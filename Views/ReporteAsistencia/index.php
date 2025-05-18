<?php /** @var array $lista */ ?>

<div class="panel-header">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Reporte de Asistencias</h3>
                <span class="opacity-75 mb-2">Muestra los reporte de asistencias con filtros</span>
            </div>
        </div>
    </div>
</div>

<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
        	<div class="d-table w-100">
        		<div class="d-table-row">
        			<div class="d-table-cell px-1">
        				<label for="fechaInicio" class="form-label">Desde </label>
        				<input type="date" class="form-control" id="fechaInicio" name="fechaInicio" data-formText="form-text-fechaInicio">
        				<div id="form-text-fechaInicio" class="form-text invalid-feedback"></div>
        			</div>
        			<div class="d-table-cell px-1">
        				<label for="hasta" class="form-label">Hasta </label>
        				<input type="date" class="form-control" id="hasta" name="hasta" data-formText="form-text-hasta">
        				<div id="form-text-hasta" class="form-text invalid-feedback"></div>
        			</div>
        			<div class="d-table-cell px-1">
        				<label for="departamento" class="form-label">Departamento</label>
        				<select name="departamento" id="departamento" class="form-select">
								<option value="0">Todos</option>
							<?php foreach ($departamentos as $departamento): ?>
								<option value="<?= $departamento->id ?>"><?= $departamento->getNombre() ?></option>
							<?php endforeach; ?>
						</select>
        				<div id="form-text-departamento" class="form-text invalid-feedback"></div>
        			</div>

					<div class="d-table-cell px1">
						<label for="turno" class="form-label">Turno </label>
						<select name="turno" id="turno" class="form-select">
							<option value="0">Todos</option>
							<?php foreach (Turno::cases() as $turno): ?>
								<option value="<?= $turno->value ?>"><?= ucfirst($turno->value) ?></option>
							<?php endforeach ?>
        					
        				</select>
						<div id="form-text-turno" class="form-text invalid-feedback"></div>
					</div>

        			<div class="d-table-cell px-1">
        				<label for="agrupar" class="form-label">Agrupar por</label>
        				<select name="agrupar" id="agrupar" class="form-select">
							<option value=""></option>
        					<option value="trabajadores">Trabajadores</option>
        					<option value="departamentos">Departamentos</option>
							<option value="turnos">Turnos</option>
        				</select>
        				<div id="form-text-agrupar" class="form-text invalid-feedback"></div>
        			</div>
        		</div>
				<div class="d-table-row">
					<hr>
				</div>
				<div class="d-table-row">
					<div class="d-table-cell">
						<table class="table table-borderless table-striped">
							<tbody>
								<?php if(isset($lista)): ?>
									<?php foreach ($lista as $item): ?>
										<tr>
											<?php foreach ($item as $data): ?>
											<td><?= $data ?></td>
											<?php endforeach ?>
										</tr>
									<?php endforeach; ?>
								<?php endif ?>
								
							</tbody>
						</table>
					</div>
				</div>
        	</div>
        </div>
    </div>
</div>


<script>
	// en la carga de la pagina coloca el rango del mes actual
	document.addEventListener("DOMContentLoaded", () => {
		if(!window.location.search){
			const fecha = new Date();
			// el mes debe obtenerse con los dos digitos
			const mes = `${fecha.getMonth()+1}`.padStart(2, '0');
			const anio = fecha.getFullYear();
			var lastDayOfMonth = new Date(fecha.getFullYear(), fecha.getMonth()+1, 0);
			document.getElementById("fechaInicio").value = `${anio}-${mes}-01`;
			document.getElementById("hasta").value = `${anio}-${mes}-${lastDayOfMonth.getDate()}`;
		}
		else{
			const params = new URLSearchParams(window.location.search);
			document.getElementById("fechaInicio").value = params.get("fechaInicio");
			document.getElementById("hasta").value = params.get("hasta");
			document.getElementById("departamento").value = params.get("departamento");
			document.getElementById("turno").value = params.get("turno");
			document.getElementById("agrupar").value = params.get("agrupar");
		}

		// si la url no tiene ningun campo get llama a enviar
		if(!window.location.search) enviar();

		const fechaInicio = document.getElementById("fechaInicio");
		const fechaHasta = document.getElementById("hasta");
		const departamento = document.getElementById("departamento");
		const turno = document.getElementById("turno");
		const agrupar = document.getElementById("agrupar");

		fechaInicio.addEventListener("change", enviar);
		fechaHasta.addEventListener("change", enviar);
		departamento.addEventListener("change", enviar);
		turno.addEventListener("change", enviar);
		agrupar.addEventListener("change", enviar);



	});
	

	function enviar (){
		setTimeout(() => {
			
			const fechaInicio = document.getElementById("fechaInicio").value;
			const hasta = document.getElementById("hasta").value;
			const departamento = document.getElementById("departamento").value;
			const turno = document.getElementById("turno").value;
			const agrupar = document.getElementById("agrupar").value;
			let location = `?fechaInicio=${fechaInicio}&hasta=${hasta}`;
			if(departamento !== "0") location += `&departamento=${departamento}`;
			if(turno !== "0") location += `&turno=${turno}`;
			if(agrupar !== "") location += `&agrupar=${agrupar}`;

			// get url witahout search params
			locationTemp = window.location.protocol + "//" + window.location.host + window.location.pathname;

			
			window.location = locationTemp + location;
		}, 500);
	}
</script>