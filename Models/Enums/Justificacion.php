<?php
enum Justificacion: int {
    case Injustificado = 1;
    case Vacaciones = 2;
    case Medico = 3;
    case Emergencia = 4;
    case Judicial = 5;
    case Enfermedad = 6;
    case Muerte_De_Un_Familiar = 6;
    case Otro = 7;

}

function getJustificacionOptions(): string{
    // option para html
    $html = '';
    foreach (Justificacion::cases() as $justificacion) {
        // remplasa los _ por espacios
        $justificacion->name = str_replace('_', ' ', $justificacion->name);
        $html .= '<option value="' . $justificacion->value . '">' . $justificacion->name . '</option>';
    }
    return $html;

}
