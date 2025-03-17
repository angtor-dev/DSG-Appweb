<?php
requiereAutenticacion();
requierePermiso("roles", "consultar");

$roles = Rol::listar(1);

renderView();