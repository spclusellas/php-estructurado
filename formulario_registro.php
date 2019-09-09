<?php
include_once('funciones_derqui.php');
include_once('paises.php');

if ($_POST) {
  // Utilizando la funcion validarRegistro voy a validar todo lo que el usuario envio. Esta funcion va a retornar errores en caso de que algun campo no hya pasado alguna validacion.
  $errores = validarRegistro($_POST);
  // En caso de no haber errores se va a continuar con l de abajo, si hubiese errores estos se desplegaran en el html.
  if (count($errores) == 0) {
      // En este punto voy a buscar el email que ingreso el usuario en nuestra base de datos. La funcion buscarPorEmail retorna toda la informacion del usuario con ese mail.
      $usuario = buscarPorEmail($_POST["email"]);
      var_dump($usuario);
      // exit;
      // En este if evaluo que en caso de que el usuario sea distinto de falso, es decir que buscarPorEmail me traiga informacion de un usuario se producira un error ya que implica que el usuario ya se encuentra registrado.
      if ($usuario != null) {
        $errores[] = "El email ya se encuentra registrado";
      } else {
        // En este caso si el usuario no fue registrado voy a utilizar las funciones aramrImagen y armarUsuario que van a armar el usuario para despues guardarlo en la base de datos.
        $avatar = armarImagen($_FILES["avatar"]);
        $usuario = armarUsuario($_POST, $avatar);
        guardarUsuario($usuario);
        // En caso de que toda la validacion sea exitosa se va a mandar al usuario a la pagina de login.
        header("Location:login.php");
        exit;
      }
  }
}


 ?>
 <!DOCTYPE html>
 <html lang="en" dir="ltr">
   <head>
     <meta charset="utf-8">
     <title>Mi Formulario</title>
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
   </head>
   <body>
     <header>
       <?php require_once('navbar.php') ?>
     </header>
     <h1>Formulario de Registro</h1>
     <ul>
       <?php if (isset($errores)): ?>
         <?php foreach ($errores as $key => $error): ?>
             <li class="alert alert-danger"><?=$error?></li>
         <?php endforeach; ?>
       <?php endif; ?>
      </ul>
      <form class="mx-a" action="formulario_derqui.php" method="post" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label><br>
        <input type="text" name="nombre" value=<?=persistir("nombre")?>>
        <br>
        <label for="apellido">Apellido</label><br>
        <input type="text" name="apellido" value=<?=persistir("apellido")?>><br>
        <label for="email">Email:</label><br>
        <input type="email" name="email" value=<?=persistir("email")?>><br>
        <label for="contrasenia">Contraseña:</label><br>
        <input type="password" name="contrasenia"><br>
        <label for="recontras">Confirmar Contraseña:</label><br>
        <input type="password" name="recontras"><br>
        <label for="sexo">Sexo:</label>
        <input type="radio" name="sexo" value="M">Mujer
        <input type="radio" name="sexo" value="H">Hombre
        <input type="radio" name="sexo" value="O">Otro
        <br>
        <label for="pais">Pais de nacimiento:</label><br>
        <select class="" name="pais">
          <?php foreach ($paises as $codigo => $pais) : ?>
            <?php if ($_POST["pais"] == $codigo): ?>
              <option value=<?=$codigo?> selected><?=$pais?></option>
              <?php else: ?>
              <option value=<?=$codigo?>><?=$pais?></option>
            <?php endif; ?>
          <?php endforeach ?>
        </select><br>
        <label for="hobbies">Hobbies:</label>
          <input type="checkbox" name="hobbies[]" value="p">Paddle
          <input type="checkbox" name="hobbies[]" value="l">Leer
          <input type="checkbox" name="hobbies[]" value="f">Futbol
          <br>
          <label for="avatar">Avatar</label>
          <input type="file" name="avatar" >
          <br>
        <button type="submit" name="button">Enviar Formilario</button>
      </form>
   </body>
 </html>
