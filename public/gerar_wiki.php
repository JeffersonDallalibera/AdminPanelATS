<?php
/**
 * Página Gerar Wiki
 */

require_once __DIR__ . '/../controllers/WikiController.php';

$controller = new WikiController();
$controller->gerarWiki();
