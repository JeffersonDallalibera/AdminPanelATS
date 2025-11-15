<?php
/**
 * Página Monitor de Tarefas
 */

require_once __DIR__ . '/../controllers/TarefaController.php';

$controller = new TarefaController();
$controller->monitor();
