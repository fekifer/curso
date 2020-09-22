<?php
error_reporting(E_ALL | E_STRICT);
ini_set("display_errors", true);

require_once 'controllers/InventarioController.php';

$controlador = new InventarioController();
echo $controlador->executeAction();
