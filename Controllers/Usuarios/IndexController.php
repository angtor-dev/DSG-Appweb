<?php
requiereAutenticacion();
requierePermiso("usuarios", "consultar");
$usuarioObj = new Usuario();
$usuarios = $usuarioObj->listarDBUser(1);

renderView();