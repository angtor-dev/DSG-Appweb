<?php
requiereAutenticacion();
requierePermiso("bitacora", "consultar");


renderView();