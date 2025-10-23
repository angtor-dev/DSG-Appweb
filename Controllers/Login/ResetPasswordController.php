<?php
cargarPost();
unset($_SESSION['usuario']);

if (!empty($_POST)) {

    if($_POST["action"] == "Enviar"){
        
        $correo = $_POST['correo'] ?? "";
        
        
        $usuario = new Usuario();
        $usuario->setterArray(["correo" => $correo]);
        echo json_encode($usuario->ResetPasswordMail());

    }
    die;
}

renderView("ResetPassEmail", "Login/");
// renderView("reset_password", "Login/template/");