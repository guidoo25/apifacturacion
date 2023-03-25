
<?php

require 'ProcesarComprobanteElectronico.php';
$procesarComprobanteElectronico = new ProcesarComprobanteElectronico();

$configCorreo = new \configCorreo();
$configCorreo->correoAsunto = "Nueva Proforma";
$configCorreo->correoHost = $_POST['correoHost'];
$configCorreo->correoPass = $_POST['correoPass'];
$configCorreo->correoPort = "465";
$configCorreo->correoRemitente = $_POST['correoRemitente'];
$configCorreo->sslHabilitado = true;

$proforma = new proforma();
$proforma->numero = "154";
$proforma->dirProformas = "C:\\Directorio";
$proforma->dirLogo ="C:\\Directorio\\logopoly.png";
$proforma->configCorreo = $configCorreo;
$proforma->razonSocial = "GUTIERREZ GOMEZ GUIDO ROBERTO"; //[Razon Social]
$proforma->nombreComercial = "";  //[Nombre Comercial, si hay]*
$proforma->ruc = "0909597536001"; //[Ruc]
$proforma->fechaEmision = "13/01/2023"; //[Fecha (dd/mm/yyyy)]
$proforma->dirMatriz = "AUTOPISTA NARCISA DE JESUS METROPOLIS 2A SOLAR 32"; //[Direccion de la Matriz ->SRI]
$proforma->dirEstablecimiento = "GUAYAQUIL"; //[Direccion de Establecimiento ->SRI]
$proforma->razonSocialComprador = "CONDOMINIO LAS LOMAS"; //Razon social o nombres y apellidos comprador
$proforma->identificacionComprador = "73333334"; // Identificacion Comprador
$proforma->direccionComprador = ""; // Identificacion Comprador
$proforma->subTotal0 = "25"; // Valor total sin impuesto
$proforma->subTotal12 = "25"; // Valor total sin impuesto
$proforma->subTotalSinImpuesto = "0.00";
$proforma->iva = "0"; // Valor total del IVA
$proforma->totalDescuento = "0.00";
$proforma->importeTotal = "45"; // Valor total con impuesto
$detalleProforma = new detalleProforma();
$detalleProforma->codigo = "COD-01"; // Codigo del Producto
$detalleProforma->descripcion = "Prueba 17 diciembre"; // Nombre del producto
$detalleProforma->cantidad = "1"; // Cantidad
$detalleProforma->precioUnitario = "10.00"; // Valor unitario
$detalleProforma->descuento = "0.00"; // Descuento u
$detalleProforma->precioTotalSinImpuesto = "10.00";

// Convertimos la cadena JSON en un array
// Obtenemos el cuerpo (body) de la solicitud
$body = file_get_contents('php://input');

// Convertimos el cuerpo de la solicitud JSON en un array asociativo

$detalles = json_decode($body, true);

// Creamos un array vacío para almacenar los detalles de proforma
$detallesProforma = array();

// Iteramos sobre el array de detalles para crear objetos detalleProforma
foreach ($detalles as $detalle) {
    $detalleProforma = new detalleProforma();
    $detalleProforma->codigo = $detalle['codigo'];
    $detalleProforma->descripcion = $detalle['descripcion'];
    $detalleProforma->cantidad = $detalle['cantidad'];
    $detalleProforma->precioUnitario = $detalle['precioUnitario'];
    $detalleProforma->descuento = $detalle['descuento'];
    $detalleProforma->precioTotalSinImpuesto = $detalle['precioTotalSinImpuesto'];
    
    // Agregamos el detalleProforma al array de detallesProforma
    $detallesProforma[] = $detalleProforma;
}


$proforma->detalles = $detallesProforma;

// Ahora puedes enviar $detalles_json en lugar de $_POST['detalles']



// Para todos los elementos que sean colecciones(mas de un dato) utilizar este esquema de array
$camposAdicionales = array();
$campoAdicional = new campoAdicional();
$campoAdicional->nombre = "Email";
$campoAdicional->valor = "yoelvysmh@gmail.com";
$camposAdicionales[0] = $campoAdicional;
$campoAdicional = new campoAdicional();
$campoAdicional->nombre = "direccion";
$campoAdicional->valor = "San isidro del inca";
$camposAdicionales[1] = $campoAdicional;
$proforma->infoAdicional = $camposAdicionales;

$procesarProforma = new procesarProforma();
$procesarProforma->proforma = $proforma;
$res = $procesarComprobanteElectronico->procesarProforma($procesarProforma);

var_dump($res);