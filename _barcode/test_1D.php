<?php

include('lib/BarcodeGenerator.php');
include('lib/BarcodeGeneratorPNG.php');
include('lib/BarcodeGeneratorSVG.php');
include('lib/BarcodeGeneratorJPG.php');
include('lib/BarcodeGeneratorHTML.php');

$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

/*
$generator = new Picqer\Barcode\BarcodeGenerator();
$generatorSVG = new Picqer\Barcode\BarcodeGeneratorSVG();
$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
$generatorJPG = new Picqer\Barcode\BarcodeGeneratorJPG();
$generatorHTML = new Picqer\Barcode\BarcodeGeneratorHTML();
*/

$data	=	$_GET['code'];

echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($data, $generator::TYPE_CODE_128)) .'">';

?>