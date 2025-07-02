<?php
requiereAutenticacion();
requierePermiso(Modulo::DEPARTAMENTOS, Permiso::CONSULTAR);

$departamentoObj = new Division();
/** @var Division[] $departamentos */
$departamentos = $departamentoObj->listar();


foreach ($departamentos as $departamento) {
    if ($departamento->idDepartamento != null) {
        $departamentoPadreArray = array_filter($departamentos, fn($d) => $d->id == $departamento->idDepartamento);
        $departamento->divisionPadre = reset($departamentoPadreArray);
    }
}



renderView();