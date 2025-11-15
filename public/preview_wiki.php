<?php
/**
 * Página Preview Wiki
 */

require_once __DIR__ . '/../controllers/WikiController.php';

$controller = new WikiController();
$controller->preview();
