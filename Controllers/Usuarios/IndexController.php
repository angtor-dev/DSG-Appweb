<?php
requiereAutenticacion();
requierePermiso("usuarios", "consultar");
$usuarioObj = new Usuario();
$usuarios = $usuarioObj->listar(1);

renderView();