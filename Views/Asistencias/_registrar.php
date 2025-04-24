<style>
	.form-info-field{
		margin-top: var(--bs-gutter-y);
		position: relative;

		display: block;
		width: 100%;
		padding: .375rem .75rem;
		font-size: 1rem;
		font-weight: 400;
		line-height: 1.5;
		color: var(--bs-body-color);
		background-color: var(--bs-body-bg);
		background-clip: padding-box;
		border: var(--bs-border-width) solid var(--bs-border-color);
		border-radius: var(--bs-border-radius);
		transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
	}
	.form-info-field::before{
		content: attr(data-info);
		display: inline-block;
		position: absolute;
		font-size: .8rem;
		top: -0.7rem;
		background-color: var(--bs-body-bg);
		padding-left: .2rem;
		padding-right: .2rem;
	}
	.form-info-field::after{
		content: "";
		display: inline-block;
	}

</style>
<div class="modal-dialog modal-lg" role="dialog" onload="eventos()">
	<div class="modal-content">
		<div class="modal-header bg-white">
			<h5 class="modal-title">MODAL_TITLE</h5>
		</div>
		<div class="container">
			<form>
				<input type="hidden" id="modificar">
				<div class="row gy-3">
					<div class="col-md-6">
						<label for="cedula" class="form-label">Cedula </label>
						<input value="0000000" type="text" class="form-control" id="cedula" name="cedula" data-span="invalid-span-cedula">
						<div id="invalid-span-cedula" class="form-text invalid-feedback"></div>
					</div>
				</div>
				<div class="row gy-3">
					<div class="col-md-6 ">
						<div class="form-info-field" data-info="Nombre" id="nombre"></div>
					</div>
					<div class="col-md-6">
						<div class="form-info-field" data-info="Departamento" id="departamento"></div>
					</div>
					<div class="col-md-6">
						<label for="fecha" class="form-label">Fecha </label>
						<input disabled type="date" class="form-control" id="fecha" name="fecha" data-span="invalid-span-fecha">
						<div id="invalid-span-fecha" class="form-text invalid-feedback"></div>
					</div>
					<div class="col-md-3">
						<label for="fechaIn" class="form-label">Hora de Ingreso</label>
						<input disabled type="time" class="form-control" id="fechaIn" name="fechaIn" data-span="invalid-span-fechaIn">
						<div id="invalid-span-fechaIn" class="form-text invalid-feedback"></div>
					</div>
					<div class="col-md-3">
						<label for="fechaOut" class="form-label">Hora de Salida</label>
						<input disabled type="time" class="form-control" id="fechaOut" name="fechaOut" data-span="invalid-span-fechaOut">
						<div id="invalid-span-fechaOut" class="form-text invalid-feedback"></div>
					</div>
				</div>
				<div class="modal-footer bg-light">
					<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal" disabled>Registrar</button>
				</div>
			</form>
		</div>
	</div>
</div>
