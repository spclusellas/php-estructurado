<?php
session_start();

// En esta funcion voy a validar cada uno de los campos que se encuentran dentro del formulario de registro. Cada uno de los if corresponde a cada una de las reglas de validacion. Podrian agragarse todavia más campos a validar.
function validarRegistro($datos){

  $errores = [];

  if ($datos) {
    if (strlen($datos["nombre"])==0) {
      $errores[] = "El campo nombre se encuentra vacio";
    }
    if (strlen($datos["apellido"])==0) {
      $errores[] = "El campo apellido se encuentra vacio";
    }
    if (!filter_var($datos["email"],FILTER_VALIDATE_EMAIL)) {
      $errores[] = "El email tiene un formato incorrecto";
    }
    if (strlen($datos["contrasenia"])<=6) {
      $errores[] ="La contraseña tiene menos de 6 caracteres";
    }
    if ($datos["contrasenia"] != $datos["recontras"]) {
      $errores[] = "Las contraseñas no coinciden";
    }
    // En esta seccion utilizo la  variable FILES para validar que la imagen que caegó el usuario haya llegado de forma correcta y tenga la extension correspondiente.
    if ($_FILES != null){
      if ($_FILES["avatar"]["error"]!=0){
        $errores["avatar"] = "No recibi la imagen";
      }
      $nombimg = $_FILES["avatar"]["name"];
      $ext = pathinfo($nombimg, PATHINFO_EXTENSION);
      if ($ext != "jpg" && $ext != "jpeg" && $ext != "png") {
        $errores["avatar"] = "La extension del archivo es incorrecto";
      }
    }
  }
  return $errores;
}

// Esta funcion se utilizara para validar que lo que ingresa el usuario en el se corresponda con lo que se encuentra en la base de datos
// La funcion va a recibir $datos que va a ser lo que llegue a travez de $_POST del formulario.
function validarLogin($datos){
  $errores = [];
  // Utilizando la funcion buscarPorEmail la cual me va a devolver toda la informacion del usuario (nombre, apellido, email, avatar) en forma de array que en este caso se lojara en la variable $usuario.
  $usuario = buscarPorEmail($datos["email"]);
  // En este if evaluo si la funcion buscarPorEmail no me trajo ningun usuario le voy a retornar que no e encontro ningun usuario.
  if ($usuario == null) {
    $errores[] = "Usuario no se encuentra registrado";
  }
  // En este if utilizando la funcion password_verify le paso lo ingresado por el usuario en el formulario y lo que se encuentra en la posicion contrasenia del usuario buscado. Esta funcion retorna true si ambas contraseñas coinciden y false si no coin ciden. En caso de ser false retornara que la contraseña es incorrecta.
  if (password_verify($datos["contrasenia"], $usuario["contrasenia"]) == false) {
    $errores[] = "La contrasenia es incorrecta";
  }
  // La funcion retorna los errores
  return $errores;
}

// Esta funcion se encargará de armar el usuario con toda la informacion que nos llegue desde el registro. Utilizara las variables $_POST y $_FILES. En la primera llega toda la informacion y en la segunda la imformacion de la imagen.
function armarUsuario($datos, $imagen){

  // Lo primero que hago es tomar la contraseña y la hasheo. Guardando esto en una variable
  $contraHash = password_hash($datos["contrasenia"], PASSWORD_DEFAULT);
  // Cre un array usuario  que en cada posicion recibira lo que el usuario ingreso en el formulario salvo en las posiciones de contraseña (va a recibir la contraseña previamente hasheada) y el avatar (recibira el nombre final de la imagen con el que fue guardado en el servidor.)
  $usuario = [
    "nombre" => $datos["nombre"],
    "apellido" => $datos["apellido"],
    "email" => $datos["email"],
    "contrasenia" => $contraHash,
    "avatar" => $imagen,
  ];
  // La funcion retorna el array usuario con cada una de sus posiciones completadas.
  return $usuario;
}

// Esta funcion recibira el usuario previamente creado en la funcion anterior y lo guardara en formato JSON en el archivo asignado.
function guardarUsuario($usuario){
  $json = json_encode($usuario);
  file_put_contents("usuarios.json",$json.PHP_EOL, FILE_APPEND);
}

function persistir($input){
  if(isset($_POST[$input])){
    return $_POST[$input];
  }
}

// Esta funcion se utiliza para buscar la base de datos que esta en formato JSON y transformarla en un array asosiativo que en cada una de sus posiciones tendrá toda la información  de cada uno de los usuarios registrados.
function abrirBaseDatos(){
    if(file_exists("usuarios.json")){

        $baseDatosJson= file_get_contents("usuarios.json");

        // Explode: Devuelve un array de string, siendo cada uno un substring del parámetro string formado por la división realizada por los delimitadores indicados en el parámetro delimiter.
        $baseDatosJson = explode(PHP_EOL,$baseDatosJson);

        //Aquí saco el ultimo registro, el cual está en blanco
        array_pop($baseDatosJson);


        //Aquí recooro el array y creo mi array con todos los usuarios
        foreach ($baseDatosJson as  $usuarios) {
            $arrayUsuarios[]= json_decode($usuarios,true);
        }

        //Aquí retorno el array de usuarios con todos sus datos
        return $arrayUsuarios;

    }else{
        return null;
    }
}

// Esta funcion se utiza para que a partir de un email recibido, busque en la base de datos al usuario asociado a ese email.
function buscarPorEmail($email){
  // Lo primero que hace esta funcion es utilizar la funcion abrirBaseDatos que nos va a devolver la base con todos los usuarios en un array asociativo
  $baseDeDatos = abrirBaseDatos();
  // Utilizando un foreach voy a recorrer toda la base de datos en donde el value va a ser un array con la info de cada uno de los usuarios.
  foreach ($baseDeDatos as $numero => $usuario) {
    // El if va a fijarse si el email ingresado en la funcion coincide con alguno de los emails dentro de la base de datos. En caso de que haya coincidencia la funcion retornara la informacion de todo el usuario, caso contrario retornara false.
    if ($email === $usuario["email"]) {
      return $usuario;
    }
  }
  return null;
}

// En esta funcion voy a recibir una imagen a traves de $_FILES y voy a guardarla con un nombre unico en una carpeta propia.
// IMPORTANTE CONOCER CADA UNA DE LAS POSICIONES DE $_FILES.
function armarImagen($imagen){
  // Aca boy a guardar el nombre con el que el usuario subio su archivo en la variable nombre para despues, utilizando la funcion pathinfo poder extraer la extension del archivo.
  $nombre = $_FILES["avatar"]["name"];
  $ext = pathinfo($nombre, PATHINFO_EXTENSION);
  // En la variable $archivoOrigen voy a guardar el archivo temporal en donde se encuentra guardada mi imagen en mi servidor.
  $archivoOrigen = $_FILES["avatar"]["tmp_name"];

  // La variable $rutaDestino va a contener toda la ruta hasta la carpeta donde guardaremos la imagen que suba el usuario.
  // La funcion dirname(__FILE__) nos va a devolver la ruta exacta hasta el lugar donde esta el archivo que estamos utilizando en este momento.
  // A esa ruta le agregué la carpeta fotos que va a ser la carpeta donde se guardaran estas imagenes
  $rutaDestino = dirname(__FILE__);
  $rutaDestino = $rutaDestino."/fotos/";

  // Utilizando la funcion uniqid() php va a crearle un nombre unico a mi imagen
  $nombreImg = uniqid();

  // En esta parte voy a guardar la ruta final de mi archivo que va a ser la ruta hastala carpeta fotos y ahi voy a ponerle el nombre creado en el paso anterios y ponerle la extension del archivo que la separe en los primeros pasos.
  $rutaDestino = $rutaDestino.".".$nombreImg.".".$ext;

  // Voy a subir el archivo que se encuentra en el tmp_name(que se guardo en la variable $archivoOrigen) en la ruta final creada en el paso anterior.
  move_uploaded_file ($archivoOrigen, $rutaDestino);

  // La funcion retornara el nombre final de la imagen con su extension.
  return $nombreImg.".".$ext;
}

// En esta funcion (si es que toda la validacion fue correcta) se le va a poner a la variable $_SESSION toda la información del usuario que inicio sesion.
function inicioSesion($usuario){
  $_SESSION["nombre"] = $usuario["nombre"];
  $_SESSION["apellido"] = $usuario["apellido"];
  $_SESSION["email"] = $usuario["email"];
  $_SESSION["avatar"] = $usuario["avatar"];
}








 ?>
