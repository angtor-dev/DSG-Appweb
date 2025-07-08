<?php
cargarPost();

requiereAutenticacion();
requierePermiso("estadisticasasistencias", "consultar");
// TODO validar permisos modificar bd





renderView("Estadisticas/tareas", "");