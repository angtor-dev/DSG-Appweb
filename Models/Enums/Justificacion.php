<?php
enum Justificacion: int {
    case Injustificado = 1;
    case Vacaciones = 2;
    case Medico = 3;
    case Emergencia = 4;
    case Judicial = 5;
    case Enfermedad = 6;
    case Muerte_De_Un_Familiar = 7;
    case Otro = 8;

}

function getJustificacionOptions(): string{
    // option para html
    $html = '';
    foreach (Justificacion::cases() as $justificacion) {
        // remplasa los _ por espacios
        $name = str_replace('_', ' ', $justificacion->name);
        $html .= '<option value="' . $justificacion->value . '">' . $name . '</option>';
    }
    return $html;

}
