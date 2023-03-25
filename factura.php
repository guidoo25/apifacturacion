
<?php

require 'ProcesarComprobanteElectronico.php';
$procesarComprobanteElectronico = new ProcesarComprobanteElectronico();
$configApp = new \configAplicacion();
$configApp->dirFirma = "C:\\Directorio\\GUIDO ROBERTO GUTIERREZ GOMEZ 061022194517.p12";
$configApp->passFirma = "Guido1966";
$configApp->dirAutorizados = "C:\\Directorio";
$configApp->dirLogo = "C:\\Directorio\\logopoly.png";

$configCorreo = new \configCorreo();
$configCorreo->correoAsunto = "Nuevo Comprobante rueb";
$configCorreo->correoHost = "smtp.gmail.com";
$configCorreo->correoPass = "cbboguewhwgacmfl";
$configCorreo->correoPort = "465";
$configCorreo->correoRemitente = "comprobantesabelinogarate@gmail.com";
$configCorreo->sslHabilitado = true;
$factura = new factura();
$factura->configAplicacion = $configApp;
$factura->configCorreo = $configCorreo;
$factura->ambiente = "1"; //[1,Prueba][2,Produccion] 
$factura->tipoEmision = "1"; //[1,Emision Normal]
$factura->razonSocial = "GUTIERREZ GOMEZ GUIDO ROBERTO"; //[Razon Social]
$factura->nombreComercial = "";  //[Nombre Comercial, si hay]*
$factura->ruc = "0909597536001"; //[Ruc]
$factura->codDoc = "01"; //[01, Factura] [04, Nota Credito] [05, Nota Debito] [06, Guia Remision] [07, Guia de Retencion]
$factura->establecimiento = "001"; //[Numero Establecimiento SRI]
$factura->ptoEmision = "100"; // [pto de emision ] **
$factura->secuencial = "000000068"; // [Secuencia desde 1 (9)]
$factura->fechaEmision = "07/03/2023"; //[Fecha (dd/mm/yyyy)]
$factura->dirMatriz = "GUAYAS / GUAYAQUIL / PASCUALES / MZ 862 SOLAR 32"; //[Direccion de la Matriz ->SRI]
$factura->dirEstablecimiento = "PASCUALES"; //[Direccion de Establecimiento ->SRI]
$factura->obligadoContabilidad = "NO"; // [SI]
$factura->contribuyenteEspecial = "";
$factura->tipoIdentificacionComprador = "05"; //Info comprador [04, RUC][05,Cedula][06, Pasaporte][07, Consumidor final][08, Exterior][09, Placa]
$factura->razonSocialComprador = "Milton Ramirez"; //Razon social o nombres y apellidos comprador
$factura->identificacionComprador = "1207118470"; // Identificacion Comprador
$factura->direccionComprador = "El Inca"; // Identificacion Comprador
//DOLAR

$body = file_get_contents('php://input');

// Convertimos el cuerpo de la solicitud JSON en un array asociativo

$detalles = json_decode($body, true);


// Creamos un array vacío para almacenar los detalles de factura
//TODO:YA FUNCION LOS DETALLES 
// Iteramos sobre el array de detalles para crear objetos detalleFactura
$detallesFactura = array();
$pagosArray = array();
$totalConImpuestoArray = array();

$total = 0; // variable para almacenar el total de la factura

foreach ($detalles as $detalle) {
    $detalleFactura = new detalleFactura();
    $detalleFactura->codigoPrincipal = $detalle['codigo'];
    $detalleFactura->descripcion = $detalle['descripcion'];
    $detalleFactura->cantidad = $detalle['cantidad'];
    $detalleFactura->precioUnitario = $detalle['precioUnitario'];
    $detalleFactura->descuento = $detalle['descuento'];
    $detalleFactura->precioTotalSinImpuesto = $detalle['precioTotalSinImpuesto'];
    
    // Sumamos el precioTotalSinImpuesto del detalle al total de la factura
    $total += $detalle['precioTotalSinImpuesto'];
    
    // Creamos un objeto impuesto para el detalle
    $impuesto = new impuesto();
    $impuesto->codigo = "2";
    $impuesto->codigoPorcentaje = "2"; // 0-0% 2-12%
    $impuesto->tarifa = "12"; // 0 0 12
    $impuesto->baseImponible = $detalle['precioTotalSinImpuesto'];
    $impuesto->valor = $detalle['precioTotalSinImpuesto'] * 0.12; // baseImponible * % impuesto
    
    // Agregamos el impuesto al detalle de factura
    $detalleFactura->impuestos = array($impuesto);
    
    // Agregamos el detalleFactura al array de detallesFactura
    $detallesFactura[] = $detalleFactura;
    
    // Agregamos el impuesto al array de impuestos

    // Creamos un objeto pago y lo agregamos al array de pagos
    $pago = new pagos();
    $pago->formaPago = "01";
    $pago->total = $detalle['precioTotalSinImpuesto'] + $impuesto->valor;
    $pagosArray[] = $pago;
}
echo "El valor total de la factura es: " . $total;

$totalImpuesto = new totalImpuesto();
$totalImpuesto->codigo = "2";
$totalImpuesto->codigoPorcentaje = "2";
$totalImpuesto->baseImponible = $total;
$totalImpuesto->valor = $total * 0.12;
$totalConImpuestoArray[] = $totalImpuesto;





$factura->totalSinImpuestos = $total; // Total sin aplicar impuestos
$factura->totalDescuento = "0.00"; // Total Dtos
/// Convertimos la cadena JSON en un array
// Obtenemos el cuerpo (body) de la solicitud HTTP; //Agrega el impuesto a la factura

$factura->propina = "0.00"; // Propina 
$factura->importeTotal = $total + $total * 0.12;
$factura->moneda = "DOLAR"; 









// Asignamos los detalles de factura, pagos e impuestos al objeto factura
$factura->detalles = $detallesFactura;
$factura->pagos = $pagosArray;
$factura->totalConImpuesto = $totalConImpuestoArray;



//asi podemos crear los elementos que sean una lista, en este caso es con campo adicional, pero puede ser con detallesFactura, 
//impuestos, etc.
//en los documentos estan los que son arreglos de campos.


//Para todos los elementos que sean colecciones(mas de un dato) utilizar este esquema de array
$camposAdicionales = array();
$campoAdicional = new campoAdicional();
$campoAdicional->nombre = "Email";
$campoAdicional->valor = "guidoroberto.2";
$camposAdicionales[0] = $campoAdicional;
$campoAdicional = new campoAdicional();
$campoAdicional->nombre = "direccion";
$campoAdicional->valor = "San isidro del inca";
$camposAdicionales[1] = $campoAdicional;

$factura->agenteRetencion = '';
$factura->regimenRimpes1 = "Contribuyente Régimen RIMPE";

$factura->infoAdicional = $camposAdicionales;
/* 
$procesarComprobante = new procesarComprobante();
$procesarComprobante->comprobante = $factura;
$procesarComprobante->envioSRI = true; //El sistema si es true 1-Crea XML en el directorio de autorizado 2-Firma XML 3-Crea Ride en el directorio autorizado 4-Envia SRI 5-Nos devuelve respuesta
$res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);

if ($res->return->estadoComprobante == "AUTORIZADO") {
    $procesarComprobante = new procesarComprobante();
    $procesarComprobante->comprobante = $factura;
    $procesarComprobante->envioSRI = false; //El sistema si es false 1-Crea XML en el directorio de autorizado 2-Firma XML 3-Crea Ride en el directorio autorizado 4-Envia Email al cliente 5-No devuelve respuesta
    $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
}
 */

/* Si queremos primero enviar al cliente el email y despues al sri utilizar este bloque
   */$procesarComprobante = new procesarComprobante();
  $procesarComprobante->comprobante = $factura;
  $procesarComprobante->envioSRI = false; //El sistema si es false 1-Crea XML en el directorio de autorizado 2-Firma XML 3-Crea Ride en el directorio autorizado 4-Envia Email al cliente 5-No devuelve respuesta
  $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
  if($res->return->estadoComprobante == "FIRMADO"){
  $procesarComprobante = new procesarComprobante();
  $procesarComprobante->comprobante = $factura;
  $procesarComprobante->envioSRI = true; //El sistema si es true 1-Crea XML en el directorio de autorizado 2-Firma XML 3-Crea Ride en el directorio autorizado 4-Envia SRI 5-No devuelve respuesta
  $res = $procesarComprobanteElectronico->procesarComprobante($procesarComprobante);
  }

echo '<pre>';
var_dump($res);
echo '</pre>';