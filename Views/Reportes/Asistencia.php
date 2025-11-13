<?php /** 
 * @var array $lista 
 * @var Division[] $departamentos
 *
 * 
 */
?>
<style>
	th small{
		font-size: 10px;
	}
</style>
<div class="panel-header">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Reporte de Asistencias/Inasistencias</h3>
                <span class="opacity-75 mb-2">Muestra los reporte de asistencias con filtros</span>
            </div>
        </div>
    </div>
</div>

<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
        	<div class="container">
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
        			<div class="col px-1">
        				<label for="departamento" class="form-label">División</label>
        				<select name="departamento" id="departamento" class="form-select">
        						<option value="">Todos</option>
        					<?php foreach ($departamentos as $departamento): ?>
        						<option value="<?= $departamento->id ?>"><?= $departamento->getNombre() ?></option>
        					<?php endforeach; ?>
        				</select>
        				<div id="form-text-departamento" class="form-text invalid-feedback"></div>
        			</div>

        			<div class="col px1">
        				<label for="turno" class="form-label">Turno </label>
        				<select name="turno" id="turno" class="form-select">
        					<option value="">Todos</option>
        					<?= Turno::getTurnosOptions(); ?>
        					
        				</select>
        				<div id="form-text-turno" class="form-text invalid-feedback"></div>
        			</div>

        			<div class="col px-1">
        				<label for="agrupar" class="form-label">Agrupar por</label>
        				<select name="agrupar" id="agrupar" class="form-select" data-formText="form-text-agrupar">
        					<option value=""></option>
        					<option value="trabajadores">Trabajadores</option>
        					<option value="departamentos">Divisiones</option>
        					<option value="turnos">Turnos</option>
        					<option value="semana">Semanal</option>
        				</select>
        				<div id="form-text-agrupar" class="form-text invalid-feedback"></div>
        			</div>
        		</div>
        	</div>
			<div class="fluid-container overflow-auto">
				<div class="d-table w-100">
					<div style="display: table-row-group;">

						<div class="d-table-row">
							<hr>
						</div>
						<style>
							#reporteAsistencia td,
							#reporteAsistencia th{
								text-align: left!important;
							}
						</style>
						<div class="d-table-row">
							<div class="container">
								<table class="table table-borderless table-striped" id="reporteAsistencia"></table>
							</div>
						</div>
					</div>
				</div>
			</div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalObservacion" tabindex="-1" aria-labelledby="modalObservacionLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalObservacionLabel">Observación de Inasistecia</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-12 col-md-4">
							<div class="form-info-field" id="modalObCedula" data-info="Cedula"></div>
						</div>
						<div class="col-12 col-md-4">
							<div class="form-info-field" id="modalObNombre" data-info="Nombre"></div>
						</div>
						<div class="col-12 col-md-4">
							<div class="form-info-field" id="modalObTurno" data-info="Turno"></div>
						</div>
						<div class="col-12 col-md-4">
							<div class="form-info-field" id="modalObDivision" data-info="<?= DEP_NAME ?>"></div>
						</div>
						<div class="col-12 col-md-4">
							<div class="form-info-field" id="modalObFecha" data-info="Fecha"></div>
						</div>
						<div class="col-12 col-md-4">
							<div class="form-info-field" id="modalObTipo" data-info="Tipo"></div>
						</div>
						<div class="col-12">
							<div class="form-info-field" id="modalObObservación" data-info="Observación"></div>
						</div>
					</div>
					
					
				</div>
			</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="cancelarModal" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
  </div>
</div>

<script>
	// mostrar el modal automaticamente para debugear
	document.addEventListener("DOMContentLoaded", () => {
		//new bootstrap.Modal(document.getElementById('modalObservacion')).show();
	});
</script>

<?php agregarScript("reporteAsistencias.js") ?>