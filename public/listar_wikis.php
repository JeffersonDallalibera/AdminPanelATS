<?php
/**
 * Página Listar Wikis
 */

require_once __DIR__ . '/../controllers/WikiController.php';

$controller = new WikiController();
$controller->listar();
