<?php 
$_layout = "Login";
// $data = [
//     'email' => "xavier@gmail",
//     'code' => "1234"
//     ]
//  ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Clave</title>

    <style>
    	*{font-family: arial}
        
    </style>
</head>
<body>
    <div style="margin: 10px; border: 1px solid #e0e7ff; border-radius: 20px; padding: 20px; box-shadow: 0 0 10px -4px #4338ca;">

        <h1 style="text-align: center">Dirección General De Servicios</h1>
        <h2 style="text-align: center">Re-establecer Contraseña</h2>
        <div style="width:97%; margin: auto;"><p style="text-align: center;font-size: 15px; padding: 20px">¡Hola <?= $data['email']; ?>!</p></div>
        <div class="item-date" style="text-align: center;">
            <span style="color: #888787; text-align: center;">El código para cambiar su Contraseña es el siguiente</span>
        </div>

        <div style="color: rgb(131, 205, 255); margin: 10px 0;">
            <div><span style="display: block; min-width: 200px; font-size: 40px; color: #4338ca; text-align: center; font-weight: bold; border: 1px solid #4338ca4a; padding: 10px; border-radius: 20px; background-color: #e0e7ff; box-shadow: 0 0 10px -4px #4338ca;"><?= $data['code']; ?></span></div>
        </div>
        <div style="color: #888787; text-align: center;">
            <span >Para re-establecer su contraseña, ingrese el código en la sección de recuperación de contraseña</span>
        </div>
    </div>

</body>
</html>