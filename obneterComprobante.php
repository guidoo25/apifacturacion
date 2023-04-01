<?php

require './ProcesarComprobanteElectronicoConsulta.php';
$procesarComprobanteElectronico = new ProcesarComprobanteElectronico();
$obtenerComprobante = new obtenerComprobante();
$obtenerComprobante->claveAcceso = "0503202301090959753600110011000000000671234567812";
$obtenerComprobante->ambiente = "2";
$res = $procesarComprobanteElectronico->obtenerComprobante($obtenerComprobante);

var_dump($res);