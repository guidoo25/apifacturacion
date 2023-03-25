<?php 
function dbconection(){
  // Nos conectamos a la Base de Datos MySQL
  $con = mysqli_connect("localhost", "root", "", "pruebadb");
 
  // Verificamos la conexión a la Base de Datos MySQL 
  if (mysqli_connect_errno()) {
      echo "Error al Conectar a la base de Datos: " . mysqli_connect_error();
  }
  return $con;
}
?>
