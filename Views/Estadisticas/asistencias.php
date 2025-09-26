
<?php
	/**
	 * @var Division[] $departamentos
	 */
?>

<style>
	.trabajador-checkbox-container{
		display: flex;
		justify-content: center;
		align-items: center;
		height: calc(100% - 32px);
	}
	.trabajador-checkbox-container input[type="checkbox"]{
		width: 20px;
		height: 20px;
	}





</style>

<div class="panel-header">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Estadísticas de Asistencias</h3>
                <span class="opacity-75 mb-2">Muestra las estadísticas de asistencias con filtros</span>
            </div>
        </div>
    </div>
</div>

<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
        	<div class="d-table w-100">
				<div style="display: table-row-group;">
					<div class="d-table-row">
						<div class="d-table-cell">
							<div class="row">
						
								<div class="col px-1">
									<label for="fechaInicio" class="form-label">Desde </label>
									<input type="date" class="form-control" id="fechaInicio" name="fechaInicio" data-formText="form-text-fechaInicio">
									<div id="form-text-fechaInicio" class="form-text invalid-feedback"></div>
								</div>
								<div class="col px-1">
									<label for="hasta" class="form-label">Hasta </label>
									<input type="date" class="form-control" id="hasta" name="hasta" data-formText="form-text-hasta">
									<div id="form-text-hasta" class="form-text invalid-feedback"></div>
								</div>
								<div class="col-auto">
									<label for="trabajador_check" class="form-label">por Trabajador</label><br>
									<div class="trabajador-checkbox-container">
										<input type="checkbox" id="trabajador_check">
									</div>
									<script>
										document.getElementById('trabajador_check').addEventListener('change', function() {
											let div = document.getElementById('col-trabajador');
											if(this.checked) {
												div.classList.remove('d-none')
												document.getElementById('departamento').selectedIndex = 0;
												document.getElementById('col-departamento').classList.add('d-none');
											}
											else{
												div.classList.add('d-none')
												document.getElementById('col-departamento').classList.remove('d-none');
												div.querySelector('input').value = '';
											}
										})
									</script>
								</div>
								<div class="col px-1 d-none" id="col-trabajador">
									<label for="trabajador" class="form-label text-nowrap">Cedula Trabajador</label>
									<input maxlength="8" type="text" class="form-control" id="cedulaTrabajador" name="cedulaTrabajador" data-formText="form-text-trabajador">
									<div id="form-text-trabajador" class="form-text invalid-feedback"></div>
								</div>
								<div class="col px-1" id="col-departamento">
									<label for="departamento" class="form-label">Division</label>
									<select name="departamento" id="departamento" class="form-select">
											<option value="">Todos</option>
										<?php 
										
										foreach ($departamentos as $departamento): ?>
											<option value="<?= $departamento->id ?>"><?= $departamento->getNombre() ?></option>
										<?php endforeach; ?>
									</select>
									<div id="form-text-departamento" class="form-text invalid-feedback"></div>
								</div>

								<div class="col px-1 d-none">
									<label for="agrupar" class="form-label">Agrupar por</label>
									<select name="agrupar" id="agrupar" class="form-select">
										<option value=""></option>
										<option value="trabajadores">Trabajadores</option>
										<option value="departamentos">Departamentos</option>
										<option value="turnos">Turnos</option>
									</select>
									<div id="form-text-agrupar" class="form-text invalid-feedback"></div>
								</div>
								<div class="col-auto px-1">
									<label class="fade no-select form-label d-block">l</label>
									<button id="filtrar-btn" class="btn btn-primary">Cargar</button>
								</div>
								<div class="col px-1">
									<label class="fade no-select form-label d-block">l</label>
									<button class="btn btn-info text-white" onclick="changeChart()">Lineal</button>
									<button class="btn btn-info text-white" onclick="changeChart('bar')">Barras</button>

								</div>
							</div>
						</div>
					</div>


					<div class="d-table-row">
						<hr>
					</div>
					<style>
						#asistenciaChart{
							width: 100%;
							max-height: 400%;
						}
					</style>
					<div class="d-table-row">
						<div style="position:relative; width: 100%; max-height: 400px;">
							<canvas id="asistenciasChart" height="400" width="400"></canvas>
						</div>
					</div>

					<div class="d-table-row">
						<hr>
					</div>
					<div class="d-table-row">
						<div class="container">
							<div class="row justify-content-center">
								<div class="col-auto p-2">
									<span style=" font-size: .8rem; ">Pico de asistencias</span>
									<div class="d-flex flex-column">
										<div class="text-center fw-bold" id="picoAsistencias"></div>
										<div class="text-center" id="mesPicoAsistencias"></div>
										<!-- <div>
											<hr>
										</div>
										<span style="font-size: .8rem;">Promedio de Asistencias</span>
										<div class="text-center fw-bold" id="promedioAsistencias"></div> -->
									</div>
								</div>
								<div class="col-auto p-2">
									<span style=" font-size: .8rem; ">Pico de Inasistencias</span>
									<div class="d-flex flex-column">
										<div class="text-center fw-bold" id="picoInasistencias"></div>
										<div class="text-center" id="mesPicoInasistencias"></div>
									</div>
								</div>
							</div>
							
							
						</div>
					</div>
					<div class="d-table-row">
						<div class="container text-center">
							<hr>
							<span class="h3">Estadisticas Generales de las Divisiones del Departamento</span></span>
						</div>
					</div>
					<div class="d-table-row">
						<div class="container">
							<div class="row">
								<div class="col-md-6 col-12">
									<canvas id="donutAsistencias"></canvas>
								</div>
								<div class="col-md-6 col-12">
									<canvas id="donutInasistencias"></canvas>
								</div>
							</div>
						</div>
					</div>
					<div class="d-table-row">
						<div class="container">
							<div class="row d-none">
								<div class="col">
									<div class="text-center">
										<hr>
										<span class="h3">Promedio de las Divisiones del Departamento</span>
									</div>
									<table class="table table-bordered w-auto mx-auto">
										<caption>Promedio de las divisiones en el periodo seleccionado</caption>
										<thead>
											<tr>
												<th>División</th>
												<th>Asistencias</th>
												<th>%</th>
												<th>Inasistencias</th>
												<th>%</th>
											</tr>
										</thead>
										<tbody id="promedioDivision">
											
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					
				</div>
        	</div>
        </div>
    </div>
</div>

<?php agregarLib("chartJs/chart.umd.js") ?>
<?php agregarScript("Estadisticas/asistencias.js") ?>




