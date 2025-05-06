<?php
requiereAutenticacion();
requierePermiso(Modulo::AREAS, Permiso::CONSULTAR);

$areaObj = new Area();
/** @var Area[] $areas */
$areas = $areaObj->listar();

foreach ($areas as $area) {
    if ($area->idArea != null) {
        $areaPadreArray = array_filter($areas, fn($a) => $a->id == $area->idArea);
        $area->areaPadre = reset($areaPadreArray);
    }
}

renderView();