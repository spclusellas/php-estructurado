<?php



session_start();

// var_dump($_SESSION["avatar"]);


 ?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Mi Perfil</title>
  </head>
  <body>
    <h1>Bienvenido <?=$_SESSION["nombre"] . " " . $_SESSION["apellido"]?></h1>

    <img width="200" src="fotos/.<?=$_SESSION["avatar"]?>" alt="">

    <div class="">
      <a href="cerrarsesion.php">Cerrar Sesion</a>
    </div>
  </body>
</html>
