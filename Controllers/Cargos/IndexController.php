<?php
cargarPost();
requiereAutenticacion();
//requierePermiso(Modulo::CARGOS, Permiso::CONSULTAR);


if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['action'])){
        if($_POST['action'] == 'LoadCargos'){


            $resp = (new Cargo())->listar();
            echo json_encode($resp);

        }
    }
    exit;
}



renderView();