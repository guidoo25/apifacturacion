
<?php


$monedas = array();



require 'ProcesarComprobanteElectronico.php';
$procesarComprobanteElectronico = new ProcesarComprobanteElectronico();




$configApp = new \configAplicacion();
$configApp->dirFirma = "C:\\Directorio\\GUIDO ROBERTO GUTIERREZ GOMEZ 061022194517.p12";
$configApp->passFirma = "Guido1966";
$configApp->dirAutorizados = "C:\\Directorio";
$configApp->dirLogo = "C:\\Directorio\\logopoly.png";
$configCorreo = new \configCorreo();
$configCorreo->correoAsunto = "Nuevo Comprobante electronico";
$configCorreo->correoHost = "smtp.gmail.com";
$configCorreo->correoPass = "npickryinvbinjdy";
$configCorreo->correoPort = "465";
$configCorreo->correoRemitente = "guidoroberto.25@gmail.com";
$configCorreo->sslHabilitado = true;

//DOLAR

// Convertimos el cuerpo de la solicitud JSON en un array asociativo

$body = file_get_contents('php://input');
$detalles = json_decode($body, true);

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
    
    $PTOemi=$detalle['ptoEmision'];
    $secuencial = $detalle['secuencial'];
    $establecimiento = $detalle['establecimiento'];
  $razonsocial=$detalle['razonSocial'];
  $ruc=$detalle['ruc'];
  $direccion=$detalle['direccion'];
  $fechaEmision=$detalle['fechaEmision'];
  $Identificacioncomprador=$detalle['rucComprador'];
  $razonSocialComprador=$detalle['razonSocialComprador'];
  $direccionComprador=$detalle['direccionComprador'];
  $formapago = $detalle['formaPago'];
  $correoComprador=$detalle['correoComprador'];
    // Agregamos el detalleFactura al array de detallesFactura
    $detallesFactura[] = $detalleFactura;
    
    // Agregamos el impuesto al array de impuestos

    // Creamos un objeto pago y lo agregamos al array de pagos
  
}


$factura = new factura();
$factura->configAplicacion = $configApp;
$factura->configCorreo = $configCorreo;
$factura->ambiente = "1"; //[1,Prueba][2,Produccion] 
$factura->tipoEmision = "1"; //[1,Emision Normal]
$factura->razonSocial = $razonsocial; //[Razon Social]
$factura->nombreComercial = "";  //[Nombre Comercial, si hay]*
$factura->ruc = $ruc; //[Ruc]
$factura->codDoc = "01"; //[01, Factura] [04, Nota Credito] [05, Nota Debito] [06, Guia Remision] [07, Guia de Retencion]
$factura->establecimiento = $establecimiento; //[Numero Establecimiento SRI]
$factura->ptoEmision = $PTOemi; // [pto de emision ] **
$factura->secuencial = $secuencial; // [Secuencia desde 1 (9)]
$factura->fechaEmision = $fechaEmision; //[Fecha (dd/mm/yyyy)]
$factura->dirMatriz = $direccion; //[Direccion de la Matriz ->SRI]
$factura->dirEstablecimiento = $direccion; //[Direccion de Establecimiento ->SRI]
$factura->obligadoContabilidad = "NO"; // [SI]
$factura->contribuyenteEspecial = "";
$factura->tipoIdentificacionComprador = "05"; //Info comprador [04, RUC][05,Cedula][06, Pasaporte][07, Consumidor final][08, Exterior][09, Placa]
$factura->razonSocialComprador = $razonSocialComprador; //Razon social o nombres y apellidos comprador
$factura->identificacionComprador = $Identificacioncomprador; // Identificacion Comprador
$factura->direccionComprador =  $direccionComprador; // Identificaci    on Comprador




$pago = new pagos();
$pago->formaPago = $formapago;
$pago->total = $total + $total * 0.12;
$pagosArray[] = $pago;
$camposAdicionales = array();
$campoAdicional = new campoAdicional();
$campoAdicional->nombre = "Email";
$campoAdicional->valor = $correoComprador;
$camposAdicionales[0] = $campoAdicional;

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

$factura->agenteRetencion = '';
$factura->regimenRimpes1 = "Contribuyente Régimen RIMPE";
$factura->infoAdicional = $camposAdicionales;





/* Si queremos primero enviar al cliente el email y despues al sri utilizar este bloque
   */
  
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

echo '<pre>';
var_dump($res);
echo '</pre>';