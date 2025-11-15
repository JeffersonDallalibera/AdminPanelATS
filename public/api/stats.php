<?php
/**
 * API: Retorna estatísticas das tarefas
 */

require_once __DIR__ . '/../../controllers/TarefaController.php';

$controller = new TarefaController();
$controller->getStats();
