<?php
/**
 * API: Atualiza o status de uma tarefa
 */

require_once __DIR__ . '/../../controllers/TarefaController.php';

$controller = new TarefaController();
$controller->updateStatus();
