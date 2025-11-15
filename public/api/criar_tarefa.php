<?php
/**
 * API: Cria uma nova tarefa
 */

require_once __DIR__ . '/../../controllers/TarefaController.php';

$controller = new TarefaController();
$controller->criar();
