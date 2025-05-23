<?php
enum Turno: string
{
    case Maniana = 'Mañana';
    case Tarde = 'Tarde';
    case Noche = 'Noche';
    case fin_semana = 'Fin de semana';
    case especial = 'Especial';
}