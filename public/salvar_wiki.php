<?php
/**
 * Página Salvar Wiki
 */

require_once __DIR__ . '/../controllers/WikiController.php';

$controller = new WikiController();
$controller->salvar();
