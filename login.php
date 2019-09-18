<?php
require_once("funciones.php");


if ($_POST) {
  // En esta fucion valido los campos de login que lo que hace basicamente es chequear de que el email se encuentra regristrado en la base de datos y si es afirmativo va a chequear si las contraseñas ingresadas y en la BDD coinciden.
  $errores = validarLogin($_POST);
  if (count($errores) == 0) {
    // En caso de no tener errores  nos traemos toda la informacion del usuario utlizando la funcion buscarPorEmail
    $usuario = buscarPorEmail($_POST["email"]);
    // Con esta funcion seteo la variable de SESSION para que contenga toda la información del usuario que se registró.
    inicioSesion($usuario, $_POST);
    // Lo envio a su perfil
    // var_dump($_COOKIE);
    // exit;
    header("Location:perfil.php");
  }
}



 ?>

 <!DOCTYPE html>
 <html lang="en" dir="ltr">
   <head>
     <meta charset="utf-8">
     <title>Login</title>
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
   </head>
   <body>
     <header>
       <?php require_once('navbar.php') ?>
     </header>
     <h1>Formulario de Login</h1>
     <?php if (isset($errores)): ?>
       <?php foreach ($errores as $key => $error): ?>
           <li class="alert alert-danger"><?=$error?></li>
       <?php endforeach; ?>
     <?php endif; ?>
     <form class="" action="login.php" method="post">
       <label for="email">Email:</label>
       <input type="email" name="email" value=""><br>
       <label for="contrasenia">Contraseña:</label>
       <input type="password" name="contrasenia" value=""><br>
       <input type="checkbox" name="recordar" value="S"> Recordar Usuario <br>
       <button type="submit" name="button">Iniciar Sesion</button>
     </form>
