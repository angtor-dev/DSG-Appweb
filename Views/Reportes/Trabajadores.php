<?php /** 
 * @var array $lista 
 * @var Division[] $departamentos
 *
 * 
 */
?>

<div class="panel-header">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Reporte de Trabajadores</h3>
                <span class="opacity-75 mb-2">Muestra los reporte de los trabajadores con filtros</span>
            </div>
        </div>
    </div>
</div>

<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
        	<div class="container">
        		<div class="row">
        		


        		
        		
        			<div class="col-12 col-md px-1">
        				<label for="fechaInicio" class="form-label">Desde </label>
        				<input type="date" class="form-control" id="fechaInicio" name="fechaInicio_" data-formText="form-text-fechaInicio">
        				<div id="form-text-fechaInicio" class="form-text invalid-feedback"></div>
        			</div>
        			<div class="col-12 col-md px-1">
        				<label for="hasta" class="form-label">Hasta </label>
        				<input type="date" class="form-control" id="hasta" name="hasta_" data-formText="form-text-hasta">
        				<div id="form-text-hasta" class="form-text invalid-feedback"></div>
        			</div>
                    <div class="col-12 col-md px-1">
                        <div class="text-nowrap text-center" title="Pertenecen actualmente al departamento">
                            <label class="form-label fade d-block">l</label>
                            <input name="fechaControl" type="checkbox" id="activo" checked>
                            <label for="activo" class="form-label">Activo</label>
                        </div>
                    </div>
                    <div class="col-12 col-md px-1">
                        <label for="departamento" class="form-label">División</label>
                        <select name="departamento_" id="departamento" class="form-select">
                                <option value="">Todos</option>
                            <?php foreach ($departamentos as $departamento): ?>
                                <option value="<?= $departamento->id ?>"><?= $departamento->getNombre() ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="form-text-departamento" class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-12 col-md px-1">
                        <label for="turno" class="form-label">Turno </label>
                        <select name="turno_" id="turno" class="form-select">
                            <option value="">Todos</option>
                            <?= Turno::getTurnosOptions(); ?>
                            
                        </select>
                        <div id="form-text-turno" class="form-text invalid-feedback"></div>
                    </div>
                    <div class="col-12 col-md px-1">
                        <label for="cargo" class="form-label">Cargo </label>
                        <select name="cargo_" id="cargo" class="form-select">
                            <option value="">Todos</option>
                            <?= Cargo::getCargosOptions(); ?>
                            
                        </select>
                        <div id="form-text-cargo" class="form-text invalid-feedback"></div>
                    </div>

                    <div class="col-12 col-md px-1">
                        <label for="agrupar" class="form-label">Agrupar por</label>
                        <select name="agrupar" id="agrupar" class="form-select">
                            <option value=""></option>
                            <option value="departamentos">Divisiones</option>
                            <option value="turnos">Turnos</option>
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

<?php agregarScript("reporteTrabajadores.js") ?>