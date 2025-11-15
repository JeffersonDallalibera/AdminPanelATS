<?php
/**
 * Página Visualizar Wiki
 */

require_once __DIR__ . '/../controllers/WikiController.php';

$controller = new WikiController();
$controller->view();
