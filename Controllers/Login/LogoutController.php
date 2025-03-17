<?php
requiereAutenticacion();
session_destroy();

Bitacora::registrar("Sesión finalizada");

header('location:'.LOCAL_DIR.'/Login');