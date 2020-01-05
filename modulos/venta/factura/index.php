<?php
require('fpdf.php');
require "../../conexion/conexion.php";
$con = conectar();
$cod_venta = $_GET["id"];

$sql1 = "select *from ventas where cod_venta = '$cod_venta' ";
$venta = mysqli_query($con, $sql1);
if ($venta) {
    $ventaCabeca = mysqli_fetch_array($venta);
    //echo "ventaObtenida";
} else {
    echo "Error al obtener venta";
}


class PDF extends FPDF
{
    // Cabecera de página



    // Pie de página
    function Footer()
    {
        $this->SetTextColor(0, 0, 0);
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

//cabecera documento
$pdf->Image('logo.png', 10, 8, 50);
// Arial bold 15
$pdf->SetFont('Arial', 'B', 18);
// Movernos a la derecha
$pdf->Cell(60);
// Título
$pdf->Cell(30, 10, 'Fact num: ', 0, 0, 'L');
$pdf->SetFont('Arial', '', 14);
$pdf->SetTextColor(14, 63, 1);
$pdf->Cell(0, 10,  $ventaCabeca["cod_venta"], 0, 0, 'L');
$pdf->Ln(10);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 15);
// Movernos a la derecha
$pdf->Cell(150);
// Título
$pdf->Cell(30, 10, 'Empresa 2191', 0, 0, 'R');
// Salto de línea
$pdf->Ln(6);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(150);
$pdf->Cell(30, 10, utf8_decode('RNC:47R8888W06T'), 0, 0, 'R');
// Salto de línea
$pdf->Ln(3);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(150);
$pdf->Cell(30, 10, utf8_decode('Calle Fonollar, num 21,'), 0, 0, 'R');
// Salto de línea
$pdf->Ln(3);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(150);
$pdf->Cell(30, 10, utf8_decode('Barcelona, 08003, España'), 0, 0, 'R');
// Salto de línea
$pdf->Ln(6);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(150);
$pdf->Cell(30, 10, utf8_decode('Tel. 674987789, Fax 687489926'), 0, 0, 'R');
// Salto de línea
$pdf->Ln(3);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(150);
$pdf->Cell(30, 10, utf8_decode('empresa2191@correo.com'), 0, 0, 'R');

// Salto de línea

$pdf->Ln(20);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(10, 163, 254);


//cabecera tabla producto
$pdf->Cell(10, 10, utf8_decode(''), 0, 0, 'L');
$pdf->Cell(80, 10, utf8_decode('Descripcion Producto'), 1, 0, 'L', true);
$pdf->Cell(30, 10, utf8_decode('Precio'), 1, 0, 'C', true);
$pdf->Cell(30, 10, utf8_decode('Cantidad'), 1, 0, 'C', true);
$pdf->Cell(30, 10, utf8_decode('Subtotal'), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 11);
$pdf->SetFillColor(255, 255, 255);
$sql1 = "select det.*, prod.nombre as nombreProducto from venta_detalle as det inner join productos as prod on det.cod_producto = prod.cod_producto  where cod_venta = '$cod_venta' ";
$ventaDet = mysqli_query($con, $sql1);
if ($ventaDet) {
    while ($row = mysqli_fetch_array($ventaDet)) {
        $pdf->Cell(10, 10, utf8_decode(''), 0, 0, 'L');
        $pdf->Cell(80, 10, utf8_decode($row["nombreProducto"]), 0, 0, 'L');
        $pdf->Cell(30, 10, utf8_decode(" $ ".number_format($row["precio"], 2, '.', ',') ), 0, 0, 'C');
        $pdf->Cell(30, 10, utf8_decode($row["cantidad"]), 0, 0, 'C');
        $pdf->Cell(30, 10, utf8_decode( " $ ".number_format($row["sub_total"], 2, '.', ',')  ), 0, 0, 'C');
        $pdf->Ln(6);
    }
}

$pdf->Ln(6);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(10, 10, utf8_decode(''), 0, 0, 'L');
$pdf->Cell(80, 10, utf8_decode(''), 0, 0, 'L');
$pdf->Cell(30, 10, utf8_decode(''), 0, 0, 'C');
$pdf->Cell(30, 10, utf8_decode('Subtotal: '), 0, 0, 'C');
$pdf->Cell(30, 10, utf8_decode('$ ' . number_format($ventaCabeca["total_sinIva"], 2, '.', ',')), 0, 1, 'L');

$pdf->Cell(10, 10, utf8_decode(''), 0, 0, 'L');
$pdf->Cell(80, 10, utf8_decode(''), 0, 0, 'L');
$pdf->Cell(30, 10, utf8_decode(''), 0, 0, 'C');
$pdf->Cell(30, 10, utf8_decode('Iva 21%: '), 0, 0, 'C');
$pdf->Cell(30, 10, utf8_decode('$ '. number_format($ventaCabeca["iva"], 2, '.', ',')), 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(10, 10, utf8_decode(''), 0, 0, 'L');
$pdf->Cell(80, 10, utf8_decode(''), 0, 0, 'L');
$pdf->Cell(30, 10, utf8_decode(''), 0, 0, 'C');
$pdf->Cell(30, 10, utf8_decode('Total: '), 0, 0, 'C');
$pdf->Cell(30, 10, utf8_decode('$ ' . number_format($ventaCabeca["total_conIva"], 2, '.', ',')), 0, 1, 'L');
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 15);
$pdf->Cell(50);
// Título
$pdf->SetTextColor(255, 4, 4);
$pdf->Cell(30, 0, '!MUCHAS GRACIAS POR SU COMPRA', 0, 0, 'c');

$pdf->Output('I', 'Factura-' . $cod_venta);
