<?php
/**
 * @var array{action: string, codigo:string, id:string} $_POST
 */
cargarPost();
requiereAutenticacion();
requierePermiso(Modulo::TURNOS, Permiso::CONSULTAR);


if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['action'])){
        if($_POST['action'] == 'LoadTurnos'){


            $resp = (new Turno())->listar();
            echo json_encode($resp);

        }
    }
    exit;
}



renderView();