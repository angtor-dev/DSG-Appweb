<?php
cargarPost();
requiereAutenticacion();


$perfil = $_SESSION["usuario"];


renderView();