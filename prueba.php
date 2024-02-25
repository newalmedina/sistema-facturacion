<?php
require "modulos/conexion/conexion.php";
$con = conectar();

$clave = "secret";
$encriptar = crypt($clave, '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

echo $encriptar;

echo "<br>";


echo $passHash;

echo password_verify($encriptar, $passHash);
echo password_verify($encriptar, $passHash);
