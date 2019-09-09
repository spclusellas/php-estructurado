<?php

if (file_exists("usuarios.json")) {
  $json = file_get_contents("usuarios.json");
  // var_dump($json);

  $datosArray = explode(PHP_EOL, $json);
  // echo "<pre>";
  // var_dump($datosArray);
  // echo "</pre>";
  // var_dump($datosArray);

  array_pop($datosArray);

  foreach ($datosArray as $numero => $json) {
    $arrayUsuarios[] = json_decode($json, true);
  }

  echo "<pre>";
  var_dump($arrayUsuarios);
  echo "</pre>";
} else {
  echo "El archivo no existe";
}






 ?>
