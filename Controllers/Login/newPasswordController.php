<?php

/**
 * @var $_SESSION['RESET_PASSWORD_EMAIL'] array{email: string, TIMESTAMP: int}
 */
cargarPost();
if(!(isset($_SESSION['RESET_PASSWORD_EMAIL']['email']) && isset($_SESSION['RESET_PASSWORD_EMAIL']['TIMESTAMP']))) {
    redirigir(LOCAL_DIR.'/Login/Logout');
}
else{
    if(!empty($_POST)) {
        if($_POST["action"] == "reset") {

            if(time() - $_SESSION['RESET_PASSWORD_EMAIL']['TIMESTAMP'] > 600) {
                echo json_encode([
                    'success' => false,
                    'message' => 'EL codigo ha expirado'
                ]);
            }
            $email = $_SESSION['RESET_PASSWORD_EMAIL']['email'];
            $password = $_POST['clave'];
            $code = $_POST['code'];
            
            $usuario = new Usuario();
            $usuario->setterArray(["correo" => $email, "clave" => $password]);
            echo json_encode($usuario->resetPassword($code));
        }
        die;
    }

    renderView("resetPass", "Login/");
}



